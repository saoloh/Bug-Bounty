# the Application Recon Part

- tip to make an emoji in vs code `win + .`
## check These
- the search functunality ✅
     - just a simple `img-onerror`
    - http://127.0.0.1:42000/#/ 

- the account Name ✅
    - when i tried `<script>alert()</script>` it gets in the DOm `\lert()`
    - i suspected it removes `<script>a` and runs the rest
    - so i injected this `\<script>d<script>ert()</script>` the DOM `\\rt()` 
    - it removes all `<script>*`
    - so i tried `<sc<script>rript>alert()</script>` the DOM `\cript>rript>alert()`
    - if i just inject `<script>` it will work and the script tag will be all around the form 
    - i will come back ❌
    - it removes the `s`
    - this one `<<script>sscript>alert('xss')</script>` hav the output `<script>alert('xss')</script>` but no xss


- image upload 
    - http://127.0.0.1:42000/profile
    - XSS in image nameEx: Upload image with name `"><img src=x onerror=alert(1)>.png`
    - XSS in image metadataEx: Add XSS payload in your image payloadCommand: `exiftool -Comment='"><img src=x onerror=alert(1)>' `
    - XSS in SVG fileWeb applications often allow SVG upload, use this SVG file:
    `https://gist.github.com/rudSarkar/76f1ce7a65c356a5cd71d058ab76a344`
    - i will come back ❌

- the recycle functunality
    - http://127.0.0.1:42000/#/recycle
    - nothing here

- New Address Functionality
    - http://127.0.0.1:42000/#/address/create
    - i will come back ❌
- payment methods
    - http://127.0.0.1:42000/#/saved-payment-methods
    -  ❌

- check-out functionality
    - http://127.0.0.1:42000/#/basket
    - i will come back ❌

- customer feedback
    - http://127.0.0.1:42000/#/contact
    - t think this gets reflected in the about us
        - http://127.0.0.1:42000/#/chatbot
    - strip all tags
    - `<iframe src="javascript:alert()">`

- complaint
    - http://127.0.0.1:42000/#/complain

- support chat
    - http://127.0.0.1:42000/#/chatbot

- photo wall
    - image + caption at the bottom
    - http://127.0.0.1:42000/#/photo-wall