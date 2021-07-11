I. How to install RabbitMQ
1. On Windows

    https://www.rabbitmq.com/

    1.1 Download Erlang:
        https://www.erlang.org/downloads

        - Select [OTP 24.0 Windows 64-bit Binary File] -> download
        - Install Erlang [otp_win64_24.0.exe]

    1.2 Download Rabbitmq Server:
        https://www.rabbitmq.com/install-windows.html

        - rabbitmq-server-3.8.19.exe

    1.3 Install management plugin for rabbitmq server:

        Start > RabbitMQ Server > RabbitMQ Command Promt, type:
            rabbitmq-plugins enable rabbitmq_management

    1.4 Open browser

        Type: localhost:15672 > Login page > Enter username/password: guest/guest

2. On MAC

    2.1 Install RabbitMQ Server & Erlang Via Homebrew

        - brew update
        - brew install rabbitmq

    2.2 Verify RabbitMQ

        - The RabbitMQ server scripts and CLI tools are installed into the `sbin` directory

        - List `rabbitmq` folder
            + ls /usr/local/Cellar/rabbitmq
                => show version of RabbitMQ server
                    /usr/local/Cellar/rabbitmq/<version>/
            + ls /usr/local/opt/rabbitmq/sbin

        - Links to binaries have been created under /usr/local/sbin:
          + Open shell profile (such as ~/.bashrc for bash or ~/.zshrc for zsh), add the following line:
            export PATH=$PATH:/usr/local/sbin

          + Restart RabbitMQ Server:
            brew services start rabbitmq

    2.3 Install management plugin for rabbitmq server:

        Open Terminal, type:
            rabbitmq-plugins enable rabbitmq_management

    2.4 Open browser

        Type: localhost:15672 > Login page > Enter username/password: guest/guest

II. RabbitMQ Tutorials
1. Simple
    P -> Q|Q|Q -> C
    <img src="https://www.rabbitmq.com/img/tutorials/python-one.png" />
2. Work Queues
    P -> Q|Q|Q -> C1
               -> C2
    <img src="https://www.rabbitmq.com/img/tutorials/python-three.png" />
3. Publish/Subscribe
    P -> X -> Q1|Q1|Q1 -> C1
           -> Q2|Q2|Q2 -> C2

    <img src="https://www.rabbitmq.com/img/tutorials/python-four.png" />
4. Routing
    <img src="https://www.rabbitmq.com/img/tutorials/python-four.png" />
5. Topics
    <img src="https://www.rabbitmq.com/img/tutorials/python-five.png" />
6. RPC
    <img src="https://www.rabbitmq.com/img/tutorials/python-six.png" />
7. Publisher Confirms

III. Commands
1. To list the exchanges on the server you can run
    - rabbitmqctl list_exchanges
2. You can list existing bindings using
    - rabbitmqctl list_bindings