# jQuery Filter Table Plugin

This plugin will add a search filter to tables. When typing in the filter, any rows that do not contain the filter will be hidden.

One can also define clickable shortcuts for commonly used terms.

See the demos at http://sunnywalker.github.com/jQuery.FilterTable

## Usage

Include the dependencies:

```html
<script src="/path/to/jquery.js"></script>
<script src="/path/to/bindWithDelay.js"></script> <!-- optional -->
<script src="/path/to/jquery.filtertable.js"></script>
<style>
.filter-table .quick { margin-left: 0.5em; font-size: 0.8em; text-decoration: none; }
.fitler-table .quick:hover { text-decoration: underline; }
td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
</style> <!-- or put the styling in your stylesheet -->
```

Then apply `filterTable()` to your table(s):

```html
<script>
$('table').filterTable(); //if this code appears after your tables; otherwise, include it in your document.ready() code.
</script>
```

## Options

| Option | Type | Default | Description |
| ------ | ---- | ------- | ----------- |
| `autofocus` | boolean | false | Makes the filter input field autofocused _(not recommended for accessibility reasons)_ |
| `callback` | function(`term`, `table`) | _null_ | Callback function after a filter is performed. Parameters: <ul><li><code>term</code> filter term (string)</li><li><code>table</code> table being filtered (jQuery object)</li></ul> |
| `containerClass` | string | filter-table | Class applied to the main filter input container |
| `containerTag` | string | p | Tag name of the main filter input container |
| `hideTFootOnFilter` | boolean | false | Controls whether the table's tfoot(s) will be hidden when the table is filtered |
| `highlightClass` | string | alt | Class applied to cells containing the filter term |
| `inputSelector` | string | _null_ | Use this selector to find the filter input instead of creating a new one (only works if selector returns a single element) |
| `inputName` | string | filter-table | Name attribute of the filter input field |
| `inputType` | string | search | Tag name of the filter input itself |
| `label` | string | Filter: | Text to precede the filter input |
| `minRows` | integer | 8 | Only show the filter on tables with this number of rows or more |
| `placeholder` | string | search this table | HTML5 placeholder text for the filter input |
| `preventReturnKey` | boolean | true | Trap the return key in the filter input field to prevent form submission |
| `quickList` | array | [] | List of clickable phrases to quick fill the search |
| `quickListClass` | string | quick | Class of each quick list item |
| `quickListGroupTag` | string | '' | Tag name surrounding quick list items (e.g., `ul`) |
| `quickListTag` | string | a | Tag name of each quick list item (e.g., `a` or `li`) |
| `visibleClass` | string | visible | Class applied to visible rows |

## Styling

Suggested styling:

```css
.filter-table .quick { margin-left: 0.5em; font-size: 0.8em; text-decoration: none; }
.fitler-table .quick:hover { text-decoration: underline; }
td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
```

There is a caveat on automatic row striping. While alternating rows can be striped with CSS, such as:

```css
tbody td:nth-child(even) { background-color: #f0f8ff; }
```

Note that CSS cannot differentiate between visible and non-visible rows. To that end, it's better to use jQuery to add and remove a striping class to visible rows by defining a callback function in the options.

```javascript
$('table').filterTable({
    callback: function(term, table) {
        table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
    }
});
```

## Dependencies

Other than jQuery, the plugin will take advantage of Brian Grinstead's [bindWithDelay](https://github.com/bgrins/bindWithDelay) if it is available.

## Change Log

### 1.5.4

- Added a return key trap to the input filter field so that pressing return in the field should not submit any forms the table may be within.
- The `preventReturnKey` option (`true` by default) has been added to allow you to switch back to the previous behavior of allowing the return key to submit forms.

### 1.5.3

- **There is a potentially significant change in functionality in this version.** While the documentation offered the `inputSelector` option, within the code it was implemented as `filterSelector`. This has been corrected to match the documentation. Note that if you were previously using the `filterSelector` option to overcome this issue, you will need to change it to `inputSelector` to use the feature with this version.

### 1.5.2

- Added an `inputSelector` option, thanks to [Pratik Thakkar](https://github.com/pratikt), which specifies a selector for an existing element to use instead of creating a new filter input field. There are some caveats of which to be aware:
    - If the element doesn't exist, a filter input field will be created as normal.
    - Because of quick lists and other options, this setting will be ignored and the filter input field will be created as normal if the resolution of the `inputSelector` returns more than one element.

### 1.5.1

- Added an `autofocus` option, thanks to [Robert McLeod](https://github.com/penguinpowernz), which is disabled by default. Note that autofocus is generally a bad idea for accessibility reasons, but if you do not need to be compliant or don't want to support accessibility users, it's a nice user experience option.

### 1.5

- **There is a potentially significant change in functionality in this version.** The callback is now called every time the search query changes. Previously it was only called when the change was a non-empty query. That is, the callback is now called when the query is cleared too.
- Additional features have been taken from [Tomas Celizna](https://github.com/tomasc)'s CoffeeScript-based fork:
    - The quick list items can now be something other than anchor tags. See the `quickListTag` and `quickListGroupTag` options.
    - The filter query field can now have a name attribute assigned to it. See the `inputName` option.
    - The class applied to visible rows is now user changeable. See the `visibleClass` option.
    - The options in the documentation have been ordered alphabetically for easier scanning.
- The internal pseudo selector is now created appropriately according to the jQuery version. (Pseudo selector generation changed in jQuery 1.8)

### 1.4

- Fixed a bug with filtering rarely showing rows that did not have a match with the search query.
- Added example pages.
- Improved inline documentation of the source code.

### 1.3.1 (in spirit)

- Added minified version of the plugin (thanks [Luke Stevenson](https://github.com/lucanos)).

### 1.3

- The functionality is not reapplied to tables that have already been processed. This allows you to call `$(selector).filterTable()` again for dynamically created data without it affecting previously filtered tables.

### 1.2

- Changed the default container class to `filter-table` from `table-filter` to be consistent with the plugin name.
- Made the cell highlighting class an option rather than hard-coded.

### 1.1

- Initial public release.

## License

(The MIT License)

Copyright (c) 2012 Sunny Walker <swalker@hawaii.edu>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
