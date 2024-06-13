# Usage
```
[hibp_checker 
  breach="Adobe" 
  apikey="abcdefghijklmnop" 
  toggle-breach="hibp-toggle-breach" 
  toggle-safe="hibp-toggle-safe" 
  input-placeholder="+49..."] 
  <b>Check if you are affected by this breach:</b> 
[/hibp_checker]
```
- Content inside the Shortcode will be placed above the Form.
- If Data is found in given Breach, the HTML Element with ID "hibp-toggle-breach" will be visible or hidden (toggles).
- If Data is NOT found in given Breach, the HTML Element with ID "hibp-toggle-safe" will be visible or hidden (toggles).

# Styling
The Generated code is the following
```html 
<div class="hibp-container">
    <div class="hibp-content"> 
        $content
    </div>
    <form id="hibp-form-$breach" action="current/url" method="post">
        <input class="hibp-input" placeholder="$input-placeholder" name="hibp_input">
        <input type="submit" class="hibp-button" name="submit" value="Check">
    </form>
</div>
```
You can add a custom CSS File via WPCode or other Code Injection Plugins to style this element.
```css 
.hibp-container {
    padding-top: 0px !important;
}

.hibp-container form {
	display: flex;
	align-items: center;
}

.hibp-input {
    border: solid black 4px !important;
}

.hibp-button {
    cursor: pointer !important;
    border: solid black 1px !important;
    border-radius: 4px !important;
    background-color: #black !important;
    color: #fff !important;
    padding-top: 8px !important;
	padding-bottom: 8px !important;
	padding-left: 18px !important;
	padding-right: 18px !important;
}

#hibp-toggle-breach {
	display: none;
}
#hibp-toggle-safe {
	display: none;
}
```