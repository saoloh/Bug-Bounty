- Bash is a Unix shell and command language. It is widely available on various operating systems, and it is also the default commandinterpreter on most Linux systems.
- Bash stands for Bourne-Again SHell. 
- you can use Bash interactively directly in your terminal
- you can use Bash like any other programming language to write scripts.
## create a  file `.sh` extention
> touch bash.sh
- In order to execute/run a bash script file with the bash shell interpreter, the first line of a script file must indicate the absolute path to the bash executable:
> #!/bin/bash
- this is called `shebang`
- it tells the OS to run the script
## Bash Hello World
```bash
#!/bin/bash
echo "hello world
```
- now if you are using linux you have to give the user the permission to execute the file
> chmod +x bash.sh
- then execute the file `./bash.sh` or `bash bash.sh`
## Bash variables