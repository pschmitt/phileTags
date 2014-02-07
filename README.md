PhileTags
========

Adds page tagging functionality to [Phile](http://philecms.github.io/Phile).
Based on [Pico Tags by Szymon Kaliski](https://github.com/szymonkaliski/Phile-Tags-Plugin), but only uses the provided hooks
and leaves the Phile core alone.

It gives you access to:
* If on a `/tag/` URL, the `current_tag` variable

## Installation

Place this repo into the `plugins` directory:

```bash
git clone https://github.com/pschmitt/phileTags /srv/http/plugins/phileTags 
```

**Important** Make a new template called `tag` which will be used when requesting a `tag/` URI.  

Activate it in `config.php`:

```php
$config['plugins'] = array(
    // [...]
    'phileTags' => array('active' => true),
); 
```


## Usage

Add a new `Tags` attribute to the page meta:

```
/*
Title: My First Blog Post
Description: It's a blog post about javascript and php
Author: Dan Reeves
Robots: index,follow
Date: 2013/10/02
Tags: js,javascript,php
*/
```

## Configuration

* You can customize which template should be used when on a `tag/` page by setting `$config['tag_template']`. 
This setting defaults to `'tag'`.

* The separator used for splitting the tag meta can also be changed by setting `$config['tag_separator']`. 
Its default value is `','`.

## Templates

You can now access both the current page `meta.tags` and each `page.tags` in the `pages` array:

```html
{% if is_front_page %}
<!-- front page -->
    {% for page in pages %}
        {% if page.date %}
            <article>
                <h2><a href="{{ page.url }}">{{ page.title }}</a></h2>
                <p class="meta">Tags:
                    {% for tag in page.tags %}
                        <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
                    {% endfor %}
                </p>
                {{ page.excerpt }}
            </article>
        {% endif %}
    {% endfor %}
<!-- front page -->
{% elseif meta.tags %}
<!-- blog post -->
    <article>
        <h2>{{ meta.title }}</h2>
        <p class="meta">Tags:
            {% for tag in meta.tags %}
                <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
            {% endfor %}
        </p>
        {{ content }}
    </article>
<!-- blog post -->

{% elseif pages and meta.title != 'Error 404' %}
<!-- tags page -->
    All tags:
    <ul class="tags">
        {% for tag in tag_list %}
        <li><a href="/tag/{{ tag }}">#{{ tag }}</a></li>
        {% endfor %}
    </ul>
    <p>Posts tagged <a href="{{ page.url }}">#{{ current_tag }}</a>:</p>
    {% for page in pages %}
        {% if page.date %}
            <article>
                <h2><a href="{{ page.url }}">{{ page.title }}</a></h2>
                <p class="meta">Posted on {{ page.date_formatted }} by {{ page.author }}
                    <span class="tags"><br />Tags:
                        {% for tag in page.tags %}
                                <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
                        {% endfor %}
                    </span>
                </p>
                {{ page.content }}
            </article>
        {% endif %}
    {% endfor %}
<!-- tags page -->
{% else %}
<!-- single page -->
<article>
    <h2>{{ meta.title }}</h2>
    {{ content }}
</article>
<!-- single page -->
{% endif %}
```

