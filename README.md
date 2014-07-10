# Opml-Parse

This plugin lets you parse an OPML file and show its contents. I wrote it to turn an exported OPML file from my RSS reader into a blogroll.

Here's the basic prototype:

    {exp:opml_parse file_path="path/to/file.opml"}
        <a href="{htmlurl}">{title}</a><br />
    {/exp:opml_parse}

There are two optional parameters:

- `limit="10"` - the number of rows you want returned:
- `backspace="6"` - the number of characters to be trimmed off the end.

The following variables are permitted:

- `{text}`
- `{description}`
- `{title}`
- `{type}`
- `{htmlurl}`
- `{xmlurl}`

## Change Log

- Version 1.1
	- Updated plugin to be 2.0 compatible
