# Recon Learning Labs

## Lab 1: Basic Subdomain Enumeration with Subfinder

*   **Objective:** Learn how to use Subfinder, a popular tool for passive subdomain discovery.
*   **Tools:** 
    *   [Subfinder](https://github.com/projectdiscovery/subfinder)
*   **Steps:**
    1.  **Installation:** If you don't have Subfinder installed, open your terminal and run the following command:
        ```bash
        go install -v github.com/projectdiscovery/subfinder/v2/cmd/subfinder@latest
        ```
        *(Note: You need to have Go installed and configured correctly.)*

    2.  **Execution:** Run Subfinder against a target domain. For this lab, you can use a public bug bounty program's domain like `example.com`.
        ```bash
        subfinder -d example.com
        ```

    3.  **Saving Output:** It's good practice to save your results.
        ```bash
        subfinder -d example.com -o example_subdomains.txt
        ```

*   **Expected Outcome:** You will see a list of subdomains for `example.com` printed to your terminal, and the same list will be saved in the `example_subdomains.txt` file. This is a foundational step in mapping out an organization's web presence.

## Lab 2: Subdomain Enumeration with Assetfinder

*   **Objective:** Discover subdomains using Assetfinder, another popular subdomain discovery tool, and compare its results with Subfinder.
*   **Tools:** 
    *   [Assetfinder](https://github.com/tomnomnom/assetfinder)
*   **Steps:**
    1.  **Installation:** If you don't have Assetfinder installed, run:
        ```bash
        go install -v github.com/tomnomnom/assetfinder@latest
        ```
        *(Ensure Go is installed and configured.)*

    2.  **Execution:** Run Assetfinder against the same target domain (`example.com`).
        ```bash
        assetfinder example.com
        ```

    3.  **Saving Output:** Save the results to a separate file.
        ```bash
        assetfinder example.com > example_assetfinder_subdomains.txt
        ```

    4.  **Comparison (Optional but Recommended):** Compare the results from Subfinder and Assetfinder to see differences and overlaps.
        ```bash
        cat example_subdomains.txt example_assetfinder_subdomains.txt | sort -u
        ```

*   **Expected Outcome:** You will get a list of subdomains from Assetfinder,
        potentially discovering some that Subfinder missed. The comparison step will help you understand that using multiple tools often yields a more comprehensive list.

## Lab 3: Probing for Live Web Servers with httpx

*   **Objective:** Learn to use `httpx` to check which of your discovered subdomains are running a live web server. This helps you focus on actual attack surface.
*   **Tools:**
    *   [httpx](https://github.com/projectdiscovery/httpx)
    *   The `all_unique_subdomains.txt` file you created in the previous lab.
*   **Steps:**
    1.  **Installation:**
        ```bash
        go install -v github.com/projectdiscovery/httpx/cmd/httpx@latest
        ```
    2.  **Execution:** Run `httpx` using your list of unique subdomains as input.
        ```bash
        cat all_unique_subdomains.txt | httpx
        ```
    3.  **Saving Output:** Save the list of live sites to a new file. The `-o` flag in `httpx` is a convenient way to do this.
        ```bash
        cat all_unique_subdomains.txt | httpx -o live_subdomains.txt
        ```

*   **Expected Outcome:** The `live_subdomains.txt` file will contain a list of URLs (e.g., `http://www.example.com`, `https://app.example.com`) that responded to `httpx`'s probes. This list is more valuable than your raw subdomain list because it represents active web applications.

## Lab 4: Visual Reconnaissance with gowitness

*   **Objective:** Learn to use `gowitness` to take screenshots of the live web servers you discovered. This allows for rapid visual assessment of a large number of websites to prioritize further investigation.
*   **Tools:**
    *   [gowitness](https://github.com/sensepost/gowitness)
    *   The `live_subdomains.txt` file from Lab 3.
*   **Steps:**
    1.  **Installation:**
        ```bash
        go install -v github.com/sensepost/gowitness@latest
        ```
    2.  **Execution:** Run `gowitness` against your list of live subdomains.
        ```bash
        gowitness scan file -f live_subdomains.txt
        ```
        > **Command Breakdown:**
        > *   **`gowitness`**: The main tool for taking website screenshots.
        > *   **`file`**: A subcommand that tells `gowitness` to read its input from a file.
        > *   **`-f live_subdomains.txt`**: The `-f` (or `--file`) flag specifies the input file, which should contain one URL per line. `gowitness` will visit each URL, take a screenshot, and save it.

    3.  **Viewing Results:** `gowitness` will create a `screenshots` directory containing the captured images. It also runs a web server to view the results in a convenient gallery.
        ```
        INFO[0005] Starting gowitness web server at: http://127.0.0.1:7171
        ```
        Open the provided URL in your browser to see the screenshots.

*   **Expected Outcome:** You will have a directory full of screenshots from the live websites. By quickly scrolling through the `gowitness` web interface, you can identify interesting applications, default pages, or error messages that might be worth investigating further.



## Lab 5: Content Discovery with Gobuster

*   **Objective:** Learn to use `gobuster` to discover hidden directories and files on web servers. This is crucial for finding unlinked content, administrative interfaces, or sensitive files that are not publicly advertised.
*   **Tools:**
    *   [Gobuster](https://github.com/OJ/gobuster)
    *   A wordlist (e.g., `common.txt` from SecLists).
*   **Steps:**
    1.  **Installation:**
        ```bash
        go install github.com/OJ/gobuster/v3@latest
        ```
    2.  **Wordlist Preparation:** Download a common wordlist. For example, from the `SecLists` repository:
        ```bash
        # If you don't have SecLists, clone it
        # git clone https://github.com/danielmiessler/SecLists.git
        cp SecLists/Discovery/Web-Content/common.txt .
        ```
    3.  **Execution:** Run `gobuster` against a target URL (e.g., one identified from `live_subdomains.txt` or `gowitness` results).
        ```bash
        gobuster dir -u http://example.com -w common.txt
        ```
        > **Command Breakdown:** 
        > *   **`gobuster`**: The main executable for the Gobuster tool.
        > *   **`dir`**: Specifies that we are performing directory/file brute-forcing. Other modes include `dns` for subdomain brute-forcing, `vhost` for virtual host discovery, etc.
        > *   **`-u http://example.com`**: The `-u` (or `--url`) flag specifies the target URL for the scan.
        > *   **`-w common.txt`**: The `-w` (or `--wordlist`) flag specifies the path to the wordlist file to use for brute-forcing. Each line in this file will be tested as a directory or file name.

    4.  **Analyzing Results:** `gobuster` will output a list of discovered directories and files, along with their HTTP status codes. Pay attention to `200 OK` (successful), `301/302 Redirect` (might lead to other interesting paths), and `403 Forbidden` (could indicate protected but existing resources).

*   **Expected Outcome:** A list of hidden directories and files found on the target web server, which can lead to further investigation and potential vulnerabilities.
**Option 1: Exclude the 302 status code**
- `gobuster dir -u https://stage.enroll.verilyme.com -w SecLists/Discovery/Web-Content/common.txt -b 302,404`
## Lab 6: Creating Custom Wordlists

*   **Objective:** Learn to create custom, target-specific wordlists for content discovery. This goes beyond using generic lists and helps you find more relevant and hidden content by using words and patterns from the target application itself.
*   **Tools:**
    *   [gospider](https://github.com/jaeles-project/gospider)
    *   A list of target domains (e.g., `live_subdomains.txt`).
*   **Steps:**
    1.  **Installation (gospider):**
        ```bash
        go install -v github.com/jaeles-project/gospider@latest
        ```
    2.  **Crawl the Target:** Use `gospider` to crawl a website and extract words. The `-s` flag is for the URL, `-o` saves the output, and `-q` silences the tool's own output so you only get the results. The `--other-source` flag finds URLs from 3rd party sources, and `--include-subs` includes subdomains.
        ```bash
        gospider -s "https://example.com" -q --other-source --include-subs -o gospider_output
        ```
    3.  **Process the Output:** The output from `gospider` and other crawlers can be messy. You need to process it to create a clean wordlist. The `unfurl` tool is great for this, but you can also use a combination of `grep`, `sed`, and `sort`.
        ```bash
        # Example of processing the output to get a wordlist
        cat gospider_output/* | grep -oE '[a-zA-Z0-9._-]+' | sed 's/\.js$//' | sort -u > custom_wordlist.txt
        ```
        This command extracts alphanumeric strings, removes `.js` extensions, and creates a unique, sorted wordlist.

    4.  **Use Your Custom Wordlist:** Now, you can use your new `custom_wordlist.txt` with a tool like `gobuster` from Lab 5.
        ```bash
        gobuster dir -u http://example.com -w custom_wordlist.txt
        ```

*   **Expected Outcome:** You will have a custom-built wordlist tailored to your target. When used with a content discovery tool, this list is more likely to find non-public or hidden directories and files compared to a generic wordlist, because it uses terms and patterns found directly on the target's website.


## Lab 7: Discovering Hidden Parameters with Arjun

*   **Objective:** Learn to use Arjun to find hidden HTTP parameters for a given URL. This is a form of fuzzing that can uncover unlinked or debug parameters, potentially leading to vulnerabilities like SQL injection, IDOR, or information disclosure.
*   **Tools:**
    *   [Arjun](https://github.com/s0md3v/Arjun)
    *   A target URL to test (e.g., from your `live_subdomains.txt`).
*   **Steps:**
    1.  **Installation:**
        ```bash
        pip3 install arjun
        ```
        *(Note: Arjun is a Python tool, so you need Python and pip installed.)*

    2.  **Execution (Single URL):** Run Arjun against a single URL. It's best to choose a URL that looks like it might have dynamic content, such as `/login`, `/profile`, or `/api/v1/user`.
        ```bash
        arjun -u https://example.com/login
        ```

    3.  **Execution (Multiple URLs):** You can also provide a file containing multiple URLs as input.
        ```bash
        # First, create a file with some target URLs
        # echo "https://example.com/forgot-password" >> target_urls.txt
        # echo "https://example.com/search" >> target_urls.txt
        
        arjun -i target_urls.txt
        ```

    4.  **Analyzing Results:** Arjun will test a large internal wordlist of parameter names against the target URL(s). It analyzes the server's response to determine if a parameter is valid. The output will be a list of discovered parameters for each URL.
        ```
        [+] 1337 parameters found for target: https://example.com/login
        [+] Parameters for https://example.com/login:
        ?next=
        ?continue=
        ?redirect_uri=
        ...
        ```

*   **Expected Outcome:** You will have a list of valid, and potentially hidden, GET/POST parameters for the tested endpoints. These parameters are your next point of interest for manual testing. You can try to inject special characters, change values, or look for unexpected behavior.


## Lab 8: Finding Endpoints in JavaScript Files with subjs

*   **Objective:** Learn to analyze JavaScript files to discover hidden API endpoints and subdomains. Modern web applications rely heavily on JavaScript, which often contains a goldmine of information about the application's structure. `subjs` is a Go-based tool that efficiently extracts this information.
*   **Tools:**
    *   [subjs](https://github.com/lc/subjs)
    *   A list of live URLs (e.g., `live_subdomains.txt` from Lab 3).
*   **Steps:**
    1.  **Installation:**
        ```bash
        go install -v github.com/lc/subjs@latest
        ```
        *(Note: You need to have Go installed and configured correctly.)*

    2.  **Execution:** Run `subjs` against your list of live URLs. It will automatically crawl the pages, find linked JavaScript files, and analyze them.
        ```bash
        cat live_subdomains.txt | subjs
        ```

    3.  **Saving Output:** You can save the results to a file for later review.
        ```bash
        cat live_subdomains.txt | subjs > js_endpoints.txt
        ```

    4.  **Analyzing Results:** `subjs` will output a list of subdomains, API endpoints, and other paths it finds within the JavaScript files. Look for interesting paths, especially those containing words like `api`, `admin`, `v1`, `private`, or `dashboard`.
        ```
        api.example.com
        internal-api.example.com
        /api/v1/users
        /api/v1/messages
        /api/v2/internal/status
        /admin/login
        /dashboard/get_user_data.php
        ```

*   **Expected Outcome:** You will have a list of potential API endpoints, new subdomains, and other hidden links discovered from the target's JavaScript files. These endpoints are often not linked anywhere else in the application and can be a great place to hunt for vulnerabilities.


## Lab 9: Technology Fingerprinting with httpx

*   **Objective:** Learn to identify the web technologies (e.g., web server, frameworks, libraries) used by a target. This is a crucial step for finding vulnerabilities, as you can search for known exploits for specific software versions. `httpx`, which you used in Lab 3, has powerful features for this.
*   **Tools:**
    *   [httpx](https://github.com/projectdiscovery/httpx)
    *   A list of live URLs (e.g., `live_subdomains.txt` from Lab 3).
*   **Steps:**
    1.  **Installation:** You should already have `httpx` installed from Lab 3. If not, refer to that lab for instructions.

    2.  **Execution:** Run `httpx` against your list of live URLs with flags to extract technology information. The `-tech-detect` flag is the key here.
        ```bash
        cat live_subdomains.txt | httpx -tech-detect -title -server -status-code
        ```
        > **Command Breakdown:**
        > *   **`-tech-detect`**: This enables the Wappalyzer-based technology detection engine to identify frameworks, libraries, and more.
        > *   **`-title`**: Extracts and displays the HTML title of the page.
        > *   **`-server`**: Extracts and displays the `Server` HTTP header, often revealing the web server software (e.g., Apache, Nginx).
        > *   **`-status-code`**: Shows the HTTP status code of the response.

    3.  **Saving Rich Output (JSON):** For better analysis, it's highly recommended to save the output in JSON format, which captures all the discovered information in a structured way.
        ```bash
        cat live_subdomains.txt | httpx -tech-detect -json -o tech_results.json
        ```
        You can then use tools like `jq` to parse this JSON file. For example, to find all sites running on Nginx:
        ```bash
        cat tech_results.json | jq 'select(.webserver == "nginx") | .url'
        ```

*   **Expected Outcome:** You will have a detailed profile of the technologies running on each live website. This information is invaluable for the next steps of vulnerability research. For example, if you identify a site running an outdated version of WordPress or a specific jQuery plugin with a known CVE, you have a direct lead for exploitation.


## Lab 10: Automated Vulnerability Scanning with Nuclei

*   **Objective:** Learn to use the data gathered on technologies (from Lab 9) to automatically scan for known vulnerabilities (CVEs), misconfigurations, and other security weaknesses using `nuclei`.
*   **Tools:**
    *   [nuclei](https://github.com/projectdiscovery/nuclei)
    *   A list of live URLs (e.g., `live_subdomains.txt` from Lab 3).
*   **Steps:**
    1.  **Installation & Template Setup:** `nuclei` relies on a community-curated set of templates. The first time you run it, it will download them automatically. It's good practice to update them periodically.
        ```bash
        go install -v github.com/projectdiscovery/nuclei/v2/cmd/nuclei@latest
        nuclei -update-templates
        ```
    2.  **Execution (Basic Scan):** Run `nuclei` against your list of live URLs. It will automatically use its templates to check for thousands of known issues.
        ```bash
        nuclei -list live_subdomains.txt -o nuclei_results.txt
        ```
        > **Command Breakdown:**
        > *   **`-list live_subdomains.txt`**: Specifies an input file containing a list of URLs to scan.
        > *   **`-o nuclei_results.txt`**: Saves the findings to a specified file.

    3.  **Execution (Targeted Scan):** The real power of `nuclei` comes from using the technology information you already have. For example, if you know a target uses WordPress, you can run only the WordPress-related templates.
        ```bash
        # First, find all your wordpress sites from Lab 9's output
        # cat tech_results.json | jq 'select(.technologies[] | contains("WordPress")) | .url' > wordpress_sites.txt

        # Now, run only wordpress templates against those sites
        nuclei -list wordpress_sites.txt -t "technologies/wordpress/" -o nuclei_wordpress_results.txt
        ```
        You can also filter by severity (`-s critical,high,medium`) or other tags.

*   **Expected Outcome:** A list of potential vulnerabilities found on your target systems. `nuclei` provides clear output on what it found, the severity, and often a reference for the vulnerability. This gives you concrete, actionable leads to investigate manually, moving from broad reconnaissance to targeted vulnerability analysis.


## Lab 11: Fast Port Scanning with Naabu

*   **Objective:** Learn to perform fast and efficient port scanning using `naabu` to discover open ports and services on identified live hosts. Open ports often indicate running services that could be vulnerable.
*   **Tools:**
    *   [naabu](https://github.com/projectdiscovery/naabu)
    *   A list of live URLs or IP addresses (e.g., extracted from `live_subdomains.txt`).
*   **Steps:**
    1.  **Installation:**
        ```bash
        go install -v github.com/projectdiscovery/naabu/v2/cmd/naabu@latest
        ```
        *(Note: You need to have Go installed and configured correctly.)*

    2.  **Preparation (Input for Naabu):** `naabu` expects a list of hosts (IP addresses or domains). You can feed it directly from your `live_subdomains.txt` or extract just the hostnames.
        ```bash
        cat live_subdomains.txt | cut -d '/' -f 3 | sort -u > hosts_for_naabu.txt
        ```

    3.  **Execution (Basic Scan):** Run `naabu` against your list of hosts. By default, it scans common ports.
        ```bash
        naabu -list hosts_for_naabu.txt -o naabu_results.txt
        ```
        > **Command Breakdown:**
        > *   **`-list hosts_for_naabu.txt`**: Specifies an input file containing a list of hosts to scan.
        > *   **`-o naabu_results.txt`**: Saves the output to a specified file.

    4.  **Execution (Specific Ports / Full Scan):** You can also specify ports to scan using `-p` (for specific ports) or `-p -` (for all ports). Be cautious with full port scans as they can be noisy and take a long time.
        ```bash
        # Scan for common web ports and a few others
        naabu -list hosts_for_naabu.txt -p 80,443,8000,8080,8443 -o web_ports_naabu.txt
        
        # Scan all ports (use with extreme caution and permission)
        # naabu -list hosts_for_naabu.txt -p - -o full_ports_naabu.txt
        ```

*   **Expected Outcome:** You will have a list of live hosts with their open ports identified. This information is critical for understanding the attack surface beyond just web applications. For example, finding an open SSH (22), FTP (21), or database (3306, 5432) port on an unexpected host could reveal a misconfiguration or an internal service exposed to the internet.


## Lab 12: Service and Version Detection with Nmap

*   **Objective:** Learn to use `nmap` to perform in-depth service and version detection on the open ports discovered in the previous lab. This is a critical step to identify the exact software and version running on a port, which is essential for finding known vulnerabilities.
*   **Tools:**
    *   [Nmap](https://nmap.org/)
    *   The output from Lab 11 (e.g., `naabu_results.txt`).
*   **Steps:**
    1.  **Installation:** `nmap` is a fundamental security tool and can be installed on most operating systems.
        *   **Kali/Debian/Ubuntu:** `sudo apt-get install nmap`
        *   **Fedora/CentOS:** `sudo yum install nmap`
        *   **macOS (with Homebrew):** `brew install nmap`
        *   **Windows:** Download the installer from the [Nmap website](https://nmap.org/download.html).

    2.  **Preparation (Input for Nmap):** `nmap` expects a list of hosts (IP addresses or domains). From the `naabu_results.txt` file (which contains `host:port` lines), extract only the unique hosts to avoid redundant Nmap full-port-range service scans on the same host.
        ```bash
        cat naabu_results.txt | cut -d ':' -f 1 | sort -u > unique_hosts_for_nmap.txt
        ```

    3.  **Execution (Service & Version Scan):** Run `nmap` against the list of unique hosts. The `-sV` flag enables version detection, and `-sC` runs default scripts to gather more information.
        ```bash
        nmap -sV -sC -iL unique_hosts_for_nmap.txt -oN nmap_results.txt
        ```
        > **Command Breakdown:**
        > *   **`nmap`**: The Nmap executable.
        > *   **`-sV`**: Enables service and version detection. Nmap will probe the open ports to determine the software and version running.
        > *   **`-sC`**: Runs the default set of Nmap Scripting Engine (NSE) scripts. These scripts can discover additional information and some common vulnerabilities.
        > *   **`-iL unique_hosts_for_nmap.txt`**: Tells Nmap to use the list of unique hosts from the `unique_hosts_for_nmap.txt` file as input.
        > *   **`-oN nmap_results.txt`**: Saves the output in Nmap's normal format to the specified file.

    3.  **Analyzing Results:** The `nmap_results.txt` file will contain a detailed report for each host that was scanned. It will show the open ports, the state of the port, the service running (e.g., `http`, `ssh`), and the version of that service (e.g., `Apache httpd 2.4.41`, `OpenSSH 8.2p1`).
        ```
        Nmap scan report for example.com (93.184.216.34)
        Host is up (0.012s latency).
        Not shown: 998 closed ports
        PORT    STATE SERVICE  VERSION
        80/tcp  open  http     Apache httpd 2.4.41 ((Unix))
        443/tcp open  ssl/http Apache httpd 2.4.41 ((Unix))
        
        Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
        Nmap done: 1 IP address (1 host up) scanned in 12.34 seconds
        ```

*   **Expected Outcome:** You will have a comprehensive map of the services and their exact versions running on your target hosts. This information is a goldmine for the next phase of your assessment. You can now take the identified versions (e.g., "Apache httpd 2.4.41") and search for public exploits, known misconfigurations, or default credentials. This moves you from pure reconnaissance into active vulnerability discovery.


## Lab 13: Finding Public Exploits with Searchsploit

*   **Objective:** Learn how to use `searchsploit` to find publicly known exploits for the services and software versions you identified with `nmap`. `searchsploit` is a command-line tool for searching the Exploit-DB database.
*   **Tools:**
    *   [Searchsploit](https://www.exploit-db.com/searchsploit)
    *   The output from Lab 12 (e.g., `nmap_results.txt`).
*   **Steps:**
    1.  **Installation & Updates:** `searchsploit` comes pre-installed on many security-focused Linux distributions like Kali Linux. If you don't have it, you can install it via `apt` or from the Exploit-DB GitHub repository. It's crucial to keep its database updated.
    `git clone https://github.com/offensive-security/exploitdb.git /d/apps/tools/exploitdb`
        ```bash
        # Update the searchsploit database
        searchsploit -u
        ```

    2.  **Execution (Searching for Exploits):** Use the information from your `nmap` scans to search for exploits. You can search for software names, versions, or both.
    - nmap output is very large, you can filter it to find potentially interesting lines. You can use a        command like grep to search for lines that contain the word "open" in your results
    file:
    `grep "open" nmap_results.txt`

        ```bash
        # Example from Lab 12 output: Apache httpd 2.4.41
        searchsploit "Apache 2.4.41"
        ```
        You can also search for more general terms.
        ```bash
        searchsploit "wordpress 5.0"
        ```

    3.  **Analyzing Results:** `searchsploit` will list any matching exploits from its database, including the description and the path to the exploit code.
        ```
        ------------------------------------------------------------------ ---------------------------------
         Exploit Title                                                    |  Path
        ------------------------------------------------------------------ ---------------------------------
         Apache httpd 2.4.41 - 'mod_proxy' AJP Proxy Request Handling RCE  | linux/remote/48497.txt
         Apache httpd 2.4.49 - 'mod_proxy' SSRF                            | multiple/remote/49988.py
        ------------------------------------------------------------------ ---------------------------------
        ```

    4.  **Examining an Exploit:** You can examine the exploit code or details using the `--examine` or `-x` flag.
        ```bash
        searchsploit -x 49988
        ```
        This will open the exploit file in your terminal for you to read and understand how it works before you attempt to use it.

*   **Expected Outcome:** You will have identified potential public exploits for the services running on your target systems. This is a critical step in assessing the exploitability of a target. The next steps would involve understanding the exploit, setting up a safe test environment, and (with explicit permission) attempting to validate the vulnerability. **Never run exploits against systems you do not have explicit permission to test.**


## Lab 14: Manual Discovery of Reflected XSS

*   **Objective:** Learn the fundamentals of manually testing for Reflected Cross-Site Scripting (XSS) vulnerabilities in web applications. This lab moves from automated scanning to hands-on testing.
*   **Prerequisites:** A list of live web applications (e.g., `live_subdomains.txt` from Lab 3).
*   **Tools:**
    *   A modern web browser (e.g., Firefox, Chrome) and its built-in Developer Tools.
*   **Steps:**
    1.  **Identify Potential Targets:** The best candidates for Reflected XSS are pages that accept user input in URL parameters and then display that input back on the page. While `live_subdomains.txt` is a good starting point, it often contains base URLs without parameters. To find more effective targets, you should use tools that discover full URL paths and their parameters.

        **Strategy for Finding URLs with Parameters:**

        *   **Method 1: Discover Existing URLs (Best for XSS)**
            Use tools like `gospider` (Lab 6) or `subjs` (Lab 8) to crawl your target and find all linked URLs. These tools excel at discovering complete URLs, including those with parameters that are ideal for XSS testing.

            ```bash
            # Example: Process gospider's output to find unique URLs with parameters
            cat gospider_output/* | grep -oE "https?://[^[:space:]]+" | grep "?" | sort -u > urls_with_params.txt
            ```
            You can then inspect the `urls_with_params.txt` file for promising targets like `https://example.com/search.php?query=test`.

        *   **Method 2: Find Hidden Parameters**
            Sometimes, a URL path exists but its parameters are not visible in links (e.g., `https://example.com/profile/`). You can use `arjun` (Lab 7) to fuzz these endpoints and discover hidden parameters.

            ```bash
            # Example: Find hidden parameters for a specific URL
            arjun -u https://example.com/profile/
            ```
            If `arjun` discovers parameters (e.g., `?user_id=`), you can then manually craft a URL and begin testing it for XSS.

        For this lab, start by generating `urls_with_params.txt` using Method 1.

    2.  **Probe for Reflection:** Take one of the target URLs and insert a unique, non-malicious string into a parameter's value.
        *   **URL:** `https://example.com/search.php?query=test`
        *   **Modified URL:** `https://example.com/search.php?query=GEMINITEST123`

        Visit the modified URL in your browser. Does the string `GEMINITEST123` appear anywhere on the page?

    3.  **Analyze the Reflection Context:** If your string is reflected, you need to understand *how* and *where* it's being placed in the HTML.
        *   Right-click on the reflected string on the page and select "Inspect Element".
        *   Alternatively, view the entire page source (`Ctrl+U` or `Cmd+Option+U`) and search for your string.

        Observe the context. Is it inside an HTML tag (`<p>GEMINITEST123</p>`), an attribute (`<input type="text" value="GEMINITEST123">`), or inside a JavaScript block (`<script>var search_term = "GEMINITEST123";</script>`)? The context determines the payload you'll use.

    4.  **Craft and Inject a Basic Payload:** Now, try to inject a basic XSS payload. The goal is to break out of the intended context and execute JavaScript. A classic, harmless payload is the `alert()` function.
        *   **If reflected inside HTML content (like a `<p>` tag):** Try to create a new HTML tag.
            `https://example.com/search.php?query=<script>alert('XSS')</script>`
        *   **If reflected inside an HTML attribute value (like `value="..."`):** You need to close the attribute first.
            `https://example.com/search.php?query="><script>alert('XSS')</script>`

    5.  **Observe the Result:** If the page is vulnerable and there's no filtering, a pop-up box with the text "XSS" should appear in your browser. This confirms you have control of JavaScript execution.

    6.  **Basic Filter Evasion (Optional):** Many sites filter for keywords like `<script>`. If your first payload doesn't work, you can try different vectors.
        *   **Using a different tag/event handler:**
            `https://example.com/search.php?query=<img src=x onerror=alert('XSS')>`
        *   **Case variation (if filtering is case-sensitive):**
            `https://example.com/search.php?query=<ScRiPt>alert('XSS')</ScRiPt>`

*   **Expected Outcome:** You will understand the manual workflow for discovering and confirming reflected XSS vulnerabilities. You'll be able to identify injection points, craft basic payloads based on the reflection context, and confirm the vulnerability in a safe, non-destructive way. This is a foundational skill for moving beyond automated tools.


## Lab 15: Manual Discovery of SQL Injection

*   **Objective:** Learn to manually test for basic SQL Injection (SQLi) vulnerabilities, focusing on error-based and boolean-based techniques.
*   **Prerequisites:** A list of live web applications with URL parameters (e.g., from `live_subdomains.txt`).
*   **Tools:**
    *   A web browser.
*   **Steps:**
    1.  **Identify Potential Targets:** Look for URLs where parameters seem to be retrieving data from a database. Parameters with numeric values are excellent candidates.
        *   Examples: `/product.php?id=123`, `/user_profile.php?user_id=5`, `/articles.php?category=3`

    2.  **Probe for Vulnerabilities (The Single Quote Test):** The simplest and most classic test is to add a single quote (`'`) to the end of a parameter's value. This is designed to break the syntax of the backend SQL query.
        *   **Original URL:** `https://example.com/product.php?id=123`
        *   **Test URL:** `https://example.com/product.php?id=123'`

    3.  **Analyze the Response for Errors:** Visit the test URL and observe the page's response. You are looking for signs that the query was broken:
        *   **Database Error Messages:** Look for explicit errors like `You have an error in your SQL syntax...`, `Unclosed quotation mark...`, or similar messages from MySQL, PostgreSQL, MSSQL, etc. This is a strong indicator of an error-based SQLi.
        *   **Generic Error Pages:** The page might return a generic `500 Internal Server Error` or a custom application error page.
        *   **Content Disappears:** The page might load, but the content that should have been retrieved from the database (like the product details) is missing.

    4.  **Confirm with Boolean-Based Logic:** If you suspect an SQLi but don't see a clear error, you can confirm it by injecting logical conditions. This is the foundation of boolean-based blind SQLi.
        *   **Test 1 (True Condition):** Inject a condition that is always true. The page should load normally, identical to the original.
            `https://example.com/product.php?id=123 AND 1=1`
        *   **Test 2 (False Condition):** Inject a condition that is always false. The page should change, either by showing an error, no content, or a "not found" message.
            `https://example.com/product.php?id=123 AND 1=2`

        If the page behaves differently for the "true" and "false" conditions, you have confirmed an SQL injection vulnerability.

    5.  **Basic Exploitation (Finding Number of Columns):** A common next step for error-based or union-based SQLi is to determine the number of columns being returned by the original query. You can do this with an `ORDER BY` clause.
        *   `https://example.com/product.php?id=123 ORDER BY 1--` (Start with 1)
        *   `https://example.com/product.php?id=123 ORDER BY 2--`
        *   `https://example.com/product.php?id=123 ORDER BY 3--`
        *   ...and so on.
        *   Continue incrementing the number until the page breaks or errors out. The last number that worked correctly is the number of columns in the query. The `--` is a comment in SQL, used here to discard the rest of the original query.

*   **Expected Outcome:** You will learn how to spot potential SQL injection points, use a single quote to test for vulnerabilities, and use boolean logic (`AND 1=1`/`AND 1=2`) to confirm SQLi without relying on visible database errors. This provides a solid foundation for more advanced manual SQL injection techniques.
