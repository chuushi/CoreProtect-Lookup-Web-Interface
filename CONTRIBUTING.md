# Contributing
I'll be humbled to get contributors on this project!  If you'd like to contribute anything to this project, you may fork this project, make changes, then create a pull request. You may add new features or fix bugs you may find in the project.

I am cleaning the code up to make it more legible.

If you're in doubt, feel free to open an issue.

# Guidelines
Here are the general and common guidelines:
- Please use spaces to indent functions.  Do not use tabs.
- Please comment anything that may become confusing or vague to others or to you over time.
- If there's any repetitive lines, use spaces to match them up.  For example:
```php
case "w": $filter['meta']['t'] -= $val[0]*604800; break;
case "d": $filter['meta']['t'] -= $val[0]*86400;  break;
case "h": $filter['meta']['t'] -= $val[0]*3600;   break;
case "m": $filter['meta']['t'] -= $val[0]*60;     break;
case "s": $filter['meta']['t'] -= $val[0];        break;
//                                              ^ this part
```

## HTML and CSS
- Use two-space indents inside elements.
- PHP code opening and ending in between HTML code should line up with the html.
  - The PHP code should start without indent in these cases and follow the PHP styling guidelines.
  - Exmple:
```php
    <!-- ... -->
    <div class="container">
      <?php
// Rejected from setup.php
if (!empty($_GET['from']) && $_GET['from'] === "setup.php" && $c['user'][$login->getUsername()]['perm'] !== 0):
      ?>
      <div class="alert alert-info alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Info:</strong> You were redirected from <code>setup.php</code> because you did not have sufficient permission.  Please consult your administrator.</div>
      <?php
endif;
// If it doesn't have write permission to the ./cache directory
if (!is_writable("./cache/")):
      ?>
      <!-- Write alert box -->
      <div class="alert alert-warning alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Notice:</strong> The directory <code>./cache/</code> is not writable. Lookup may take marginally longer to process, and autocomplete will not have updated data. Please refer to readme.md for setup information.</div>
      <?php
endif;
      ?>
      <!-- ... -->
    </div>
    <!-- ... -->
```

## JavaScript and PHP
- Use four-space indents.
- All non-code comments should have at least a space following the comment indicaters.
  - Any comment made to debug code should have no space between the comment indicator and the code.
```php
// This is a comment
/* this too */
# so is this.
//$some-variable = "some string"
// The above is a commented-out code.
```
- Add space between if and the parantheses.

- PHP
  - Every array must use `array()`.

- JavaScript
  - The coding style on JavaScript must follow the "use strict" directive requirements.
  - All if/else statements must be followed by braces.  For example:
```js
if (this) {
    // do this;
} else {
    // do that;
}
```
