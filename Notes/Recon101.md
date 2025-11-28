# get all the subdomain for a website that have a certificate


```
curl -s "https://crt.sh/?q=%25.[WEBSITE_DOMAIN]&output=json" \
| jq -r '.[.name_value] \
| sort -u > [DATE].txt
```
- why did we store the result in a file that has the date as his name so we can use `dif` to compare it wit hresult from other days to check if they added a new domains 

- ! it is impotrant to take care that some subdomains have a `*` before them this means that the subdomain have subdomains

- another note that subfinder and sublister uses methods like this to collect subdomains

---

## another way using ASn

1. get the ip of the company `dig -short microsoft.com`
2. get the AS `whois -h whois.cymru.com` * -v [ip-from-previos-step]
3. go to `cloud.projectdiscovery.io` they will give you an API
4. open asnmap and add the API to it and use the tool this tool gives you all the ip's for the company `asnmap -h`
5. Ex of using it is `asnmap -a AS[AS-number-from-step2] -o ASip.txt`
6. `cat ASip.txt | httpx` will give you all working sites on those ip's

--- 

### third way

- just use tools like `subfinder -h` and `dnsx -h`

---

## fourth way -fav icon-

1. go to `favicon-hash-kmsec.uk`
2. give it a domain of microsoft that contains the `fav-icon` click hash from URL
3. take the `favicon_hash` and search shodan
    - take into your account that this gives you all the sites that have the icon and it may be a company that is using hte comapny service 
    - so you have to specify the domain you are searching for `org:"comany name"` EX:- microsoft will be `microsoft limited`

---

# Recon.exe: 403/404 Access, GoSpider, JS Hunting, Stored XSS, Admin Panel & AWS S3 Finds


## what happens if you encountered a 404-403 error page ?

- of course you try to bypass it
    - to do this use the wayback-machine extention

### gospider
- it crawels the websites for subdomains - hidden endpoints - etc
- `gospider -s [domain]`
- the simple output is the type of the data then the link
![alt text](image.png)
- to get it in a good format `gospider -s [domain] | grep -oE 'https?://[^[:space:]]+'`
- this regex works by geting the `http` or `https` follwed by `://` then the rest 

- if you only want js files (i don't know why :) `gospider -s [domain] | grep -oE 'https?://[^[:space:]]+' | grep '\.js$ `
- it gets anything that ends with `.js`
    you can then take all the js links and open them by `bulk URL opener` and check them manually (i don't know why :)

- you can use `gospider -s [domain] -a -r ` this passive flag to get 404/403 pages 
- if you have more time use the depth flag to crawel deeper ` gospider -s [domain] -d [number-od-depth]`

- you can use this to search for tokens and API keys
    - not recommended because i am lazy :)
![alt text](image-1.png)


- finally use `find something extention` to find d=hidden path from the page source

---
# Recon 101

- `sort -u [file] > [file]` to remove duplicates