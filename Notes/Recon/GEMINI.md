# Directory Overview

This directory contains notes and educational materials related to bug bounty hunting, with a specific focus on the reconnaissance phase. The files provide both foundational knowledge and practical, hands-on labs for learning how to discover and analyze a target's assets.

# Key Files

*   **`Bash101.md`**: Provides a brief introduction to the Bash shell, covering fundamental concepts like creating scripts, the shebang, and variables. It serves as a primer for the scripting that is often involved in security tasks.
*   **`recon_labs.md`**: A detailed, step-by-step guide with a series of labs for learning reconnaissance techniques. It walks through the installation and use of essential bug bounty tools, including:
    *   **Subdomain Enumeration:** Using `subfinder` and `assetfinder`.
    *   **Live Host Discovery:** Using `httpx` to identify active web servers.
    *   **Visual Recon:** Using `gowitness` to take screenshots of websites for quick visual assessment.
    *   **Content Discovery:** Using `gobuster` to find hidden directories and files.
    *   **Custom Wordlist Creation:** Using `gospider` to create target-specific wordlists.

# Usage

This directory is intended for self-study and reference for individuals learning about bug bounty hunting. The user can follow the labs in `recon_labs.md` to gain practical experience with common reconnaissance tools and workflows. The `Bash101.md` file can be used as a quick reference for basic shell scripting. See the "Platform Note" below for information on running the lab commands.

# Platform Note

The commands in the labs are written using a Unix-style shell syntax (common on Linux and macOS). Since you are using Windows, you will need a compatibility layer to run these commands as written. It is highly recommended to use one of the following options:

*   **Windows Subsystem for Linux (WSL):** This allows you to run a full Linux distribution directly on Windows. This is the most robust solution for compatibility.
*   **Git Bash:** A terminal emulator that provides a Bash environment on Windows, which is sufficient for most of the tools and commands in these labs.
