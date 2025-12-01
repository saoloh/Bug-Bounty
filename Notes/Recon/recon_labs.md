
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

*   **Expected Outcome:** You will get a list of subdomains from Assetfinder, potentially discovering some that Subfinder missed. The comparison step will help you understand that using multiple tools often yields a more comprehensive list.

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
