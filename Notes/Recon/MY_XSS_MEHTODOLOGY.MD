
## 🛠️ Phase 1: Subdomain Enumeration with Subfinder

The goal here is to expand your attack surface by finding all possible subdomains for the target. Subdomains often run different, potentially older or less secure applications.

  * **What it does:** **Subfinder** is a fast, passive subdomain enumeration tool that uses various sources (like Certificate Transparency logs, search engines, and passive DNS) to discover subdomains.

  * **Command:**

    ```bash
    subfinder -d target.com -o subdomains.txt
    ```

      * `-d target.com`: Specifies the target domain.
      * `-o subdomains.txt`: Outputs the discovered subdomains to a file.

  * **Next Step:** Once you have the list of subdomains, you should check which ones are actually alive (resolvable and running a web server) using a tool like `httpx`.

## 🕷️ Phase 2: Finding Endpoints and Parameters with GoSpider

After gathering subdomains, you need to crawl them to find all the URLs and potential input fields (parameters) that the application uses.

  * **What it does:** **GoSpider** is a fast web crawling tool that recursively scans a target, extracting URLs from the HTML and even JavaScript files. This is essential for finding all the possible *sinks* where your XSS payload could end up.
  * **Command:** To use GoSpider efficiently, you'll want to iterate through your list of live subdomains:
    ```bash
    cat subdomains.txt | httpx -silent | gospider -o results -c 10 -d 3 | tee all_urls.txt
    ```
    - get all http and https domains
    `cat all_urls.txt | grep -oE 'https?://[^[:space:]]+'`
    - get all domains that have params
    `cat all_urls.txt | grep -E "(\?|=)" | tee final_xss_targets.txt`
    - combined
    `cat all_urls.txt | grep -oE 'https?://[^[:space:]]+' | grep -E "(\?|=)"  | tee final_xss_targets.txt`

      * `cat subdomains.txt | httpx -silent`: Pipes the list of subdomains to `httpx` to get only the live, fully qualified URLs (e.g., `http://sub.target.com`).
      * `gospider -o results -c 10 -d 3 -s`:
          * `-s`: Site/URL to crawl (taken from the pipe).
          * `-o results`: Output directory.
          * `-c 10`: Concurrency level (how many requests at once).
          * `-d 3`: Crawling depth.
      * `| tee all_urls.txt`: Saves the output to a file while also displaying it.
  * **Filtering:** The output will contain many URLs. You should filter this list to extract only the ones that contain query parameters (e.g., `?id=`, `?search=`). This is your list of potential injection points.

## 🎯 Phase 3: Fuzzing for Hidden Parameters with Arjun

Crawl results will only show parameters visible in existing links. Many parameters are **hidden** or **unreferenced** (not linked anywhere) but still accepted by the application. This is where **Arjun** comes in.

  * **What it does:** **Arjun** is a parameter discovery tool that fuzzes (brute-forces) a given URL with a massive list of common parameter names to see which ones the application accepts or reacts to.

  * **Command (against key URLs):** You should select a few high-value base URLs from your crawl results and run Arjun against them.

    ```bash
    arjun -u "https://target.com/page?p=1"
    ```

      * `-u "URL"`: The URL you want to test for hidden parameters.

  * **Chaining for Scale:** For a more comprehensive scan across all collected URLs:

    1.  Use a tool like `waybackurls` (mentioned below) to get a large set of historical URLs.
    2.  Filter this list for base URLs (the URL up to the `?`):
        ```bash
        cat waybackurls.txt | unfurl domains | sort -u > domains_only.txt
        arjun -l domains_only.txt -o arjun_params.txt
        ```
          * `-l`: Reads a list of URLs from a file.

-----

## 🚀 Other Important Recon Steps

### 1\. Historical Data and Passive Recon (Waybackurls)

Always look at the application's history. Old, deprecated, or forgotten features are goldmines for vulnerabilities.

  * **Tool:** **waybackurls**
  * **What it does:** Fetches all historical URLs saved by the Wayback Machine for a given domain, including outdated parameters and endpoints that may still be functional.
  * **Command:**
    ```bash
    echo target.com | waybackurls > wayback_urls.txt
    ```

### 2\. Hunting for Reflected Parameters (Katana/Waybackurls + Grep)

The most efficient way to get a final list of targets for XSS is to find URLs that contain parameters.

  * **Find URLs with parameters:**
    ```bash
    cat all_urls.txt | grep '=' | tee final_xss_targets.txt
    ```
    This filters your crawled and historical URL lists for anything containing an equals sign (`=`), which usually indicates a parameter.

### 3\. Fuzzing & Exploitation (The Next Step)

Once you have your `final_xss_targets.txt` file, the actual XSS testing begins.

  * **Tool:** **Dalfox** is a highly effective tool for automatically detecting XSS vulnerabilities.
  * **Action:** Pipe your list of potential targets into a vulnerability scanner.
    ```bash
    cat final_xss_targets.txt | dalfox pipe -o xss_results.txt
    ```
    Dalfox will test each URL and parameter with various XSS payloads.



That's a smart pivot\! Combining passive (like Subfinder) and **active** reconnaissance is the best way to uncover the maximum number of subdomains and broaden your XSS attack surface.

Active enumeration involves directly interacting with the target's DNS servers, which is louder but often reveals hidden or lesser-known assets that passive scans miss.

Here is the essential workflow for active subdomain enumeration:

## ⚡ Phase 1: DNS Brute-Forcing with Puredns

DNS brute-forcing uses a wordlist of common subdomain names (like `admin`, `api`, `dev`, `staging`) and checks if they resolve to an IP address for your target domain.

  * **Tool:** **Puredns** is excellent for this. It's a fast domain resolver and brute-forcing tool that automatically uses the highly efficient **MassDNS** as its backbone. Crucially, it has built-in logic to filter out **wildcard DNS records** (where everything resolves), which typically generate massive false positives.

### 1\. The Wordlist is Key

The success of brute-forcing depends entirely on the quality of your wordlist. Use an extensive, proven list like those from the **SecLists** repository or one specifically curated for DNS/subdomains (e.g., Assetnote's wordlist).

### 2\. The Command

You need your target domain and your chosen wordlist.

```bash
# Ensure you have a good list of public DNS resolvers (e.g., in resolvers.txt)
# The '-r' flag is to specify your resolvers file
puredns bruteforce /path/to/wordlist.txt target.com -r resolvers.txt -o active_subs.txt
```

  * **`bruteforce`**: Tells Puredns to append every line in the wordlist to your domain and check if it resolves.
  * **`-r resolvers.txt`**: Specifies the list of DNS servers to query.
  * **`-o active_subs.txt`**: Saves the resolved (active) subdomains to a file.

-----

## 🚀 Phase 2: Target-Aware Permutation (Alterx)

After your initial passive (Subfinder) and general brute-force scans, you'll have a list of confirmed subdomains. Now, you can use these existing names to generate **new, highly relevant subdomains**.

  * **Tool:** **Alterx** (or similar tools like `Gotator`) takes your current subdomains and applies intelligent permutation rules to them.

### 1\. Generate Permutation List

First, combine your passive and active results.

```bash
cat subdomains.txt active_subs.txt | sort -u > all_found_subs.txt
```

Now, feed that list into Alterx to generate a new, more targeted wordlist of *potential* subdomains:

```bash
alterx -l all_found_subs.txt -p '{{word}}-{{year}}' -o permuted_wordlist.txt
```

  * The pattern `'{{word}}-{{year}}'` might turn `api.target.com` into `api-2025.target.com` or `dev-api.target.com` into `dev-api-2025.target.com`. You can use many different patterns based on common naming conventions.

### 2\. Resolve the Permutations

Next, you need to use Puredns again, but this time in resolve mode, to check which of these new, highly customized names actually resolve:

```bash
puredns resolve permuted_wordlist.txt -r resolvers.txt -o final_active_subs.txt
```

-----

## 🕸️ Phase 3: Virtual Host Fuzzing (FFUF)

Some subdomains might be hosted on the same IP address as the main domain but are only accessible if you specify the correct name in the **HTTP `Host` header**. These are called **Virtual Hosts (vhosts)** and won't show up in DNS enumeration.

  * **Tool:** **FFUF** (Fuzz Faster U Fool) is a powerful, fast fuzzer that can be used to brute-force the `Host` header.

### The Command

You will fuzz the `Host` header against the main IP address of the target.

```bash
# 1. Get the IP of the target's main domain (using dig or a similar tool)
TARGET_IP=$(dig +short target.com | head -n 1)

# 2. Fuzz the Host header using your combined subdomain list as the wordlist
ffuf -w all_found_subs.txt -H "Host: FUZZ.target.com" -u http://$TARGET_IP/ -fs 1234
```

  * **`-w all_found_subs.txt`**: Uses your aggregated list of subdomains as the wordlist.
  * **`-H "Host: FUZZ.target.com"`**: The key part; the `FUZZ` keyword is replaced by FFUF with entries from your wordlist.
  * **`-u http://$TARGET_IP/`**: Specifies the base URL/IP to send the requests to.
  * **`-fs 1234`**: Filters the results based on the response size (you'll want to adjust this filter in real-time to find unique results).

Dealing with HTTP **403 Forbidden** and **404 Not Found** errors is a crucial part of bug bounty hunting, as they often hide accessible or exploitable endpoints. You shouldn't just assume they are dead ends.

Here is a structured approach for handling these errors, turning them from roadblocks into potential leads.

-----

## 🛑 Handling 403 Forbidden Errors (Access Denied)

A **403 Forbidden** error means the web server received and understood the request, but it refuses to authorize it. This often means the content exists, but you lack the necessary permissions. This is where you focus on **Bypassing Access Controls**.

### 1\. Header Manipulation

Try modifying HTTP headers to trick the application into thinking you are making the request from a trusted source or with higher privileges.

  * **Change the `X-Forwarded-For` Header:** Sometimes, access is restricted based on the client's IP address. Using this header can spoof an internal IP.
    ```
    X-Forwarded-For: 127.0.0.1
    ```
  * **Change the `Host` Header:** Some applications check the `Host` header for whitelisting. Try using subdomains that might be trusted.
  * **Add Authorization/Token Headers:** If you are authenticated, ensure your session cookies or tokens are included and try manipulating them (e.g., changing a user role value).

### 2\. HTTP Method Tampering

The server might restrict the **GET** method but allow others. This is a common misconfiguration.

  * Try changing the request method from `GET` to **`POST`**, **`HEAD`**, **`OPTIONS`**, or **`TRACE`**.
      * **Example:** A page might block `GET /admin`, but allow `POST /admin` if the developer forgot to restrict the POST handler.

### 3\. Path Manipulation (Normalization/Directory Traversal)

The application might use a simple string match to block access. Try obfuscating the path to bypass this filter.

  * **URL Encoding:** Try encoding slashes (`/` becomes `%2f` or `%252f`).
  * **Adding Path Traversal:** Try adding extra or relative path elements.
      * **Original:** `/admin/dashboard`
      * **Try:** `/admin/./dashboard`
      * **Try:** `/admin/../admin/dashboard`
  * **Trailing Slash:** Sometimes, simply adding a trailing slash or removing it works:
      * **Original:** `/admin`
      * **Try:** `/admin/`

-----

## 🔎 Handling 404 Not Found Errors (Content Discovery)

A **404 Not Found** error means the server cannot find the requested resource. When hunting for XSS, you often encounter 404s when fuzzing for hidden directories or files. This is where you focus on **Fuzzing and Guessing**.

### 1\. Aggressive Directory/File Fuzzing

Your initial crawls (GoSpider) are often not deep enough. Use a dedicated fuzzer to brute-force directories and files that might be forgotten or unlinked.

  * **Tool:** **FFUF** (Fuzz Faster U Fool) or **Wfuzz**.
  * **Target:** Use common wordlists like **DirBuster**, **SecLists**, or **Common Speak** against the domain.
    ```bash
    ffuf -w /path/to/wordlist.txt -u https://target.com/FUZZ
    ```
      * **Focus on files:** Pay special attention to files that might contain sensitive information or code, such as `phpinfo.php`, `.git/`, `.env`, `backup.zip`, or any leftover files from deployment.

### 2\. VHost Fuzzing

As covered previously, a page that returns a **404** might actually exist as a **Virtual Host (VHost)** on the server.

  * Run a tool like **FFUF** to fuzz the `Host` header against the main server IP using a list of known or guessed subdomains. If a guessed subdomain resolves to content instead of a 404, you've found a hidden endpoint.

### 3\. Check for Custom 404 Pages

When a 404 page **reflects user input**, it's an excellent candidate for **Reflected XSS**.

  * **Test:** Navigate to a clearly non-existent page and inject a test XSS payload in the URL path.
      * **Example:** `https://target.com/nonexistentpage/<script>alert(1)</script>`
  * If the `<script>alert(1)</script>` appears *unescaped* in the resulting 404 error page, you have found an XSS sink.
