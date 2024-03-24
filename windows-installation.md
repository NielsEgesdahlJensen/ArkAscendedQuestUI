# Installing PHP, mysqli, zip, and Composer on Windows

This guide will walk you through the steps to install PHP, enable the mysqli and zip modules, and install Composer on a Windows machine.

## Prerequisites

Before you begin, make sure you have the following:

- Windows operating system
- Administrative access to your machine

## Step 1: Download PHP

1. Go to the [PHP for Windows](https://windows.php.net/download/) download page.
2. Choose the PHP version that matches your system architecture (x86 or x64) and download the ZIP package.
3. Extract the contents of the ZIP package to a directory of your choice (e.g., `C:\php`).

## Step 2: Configure PHP

1. Rename the `php.ini-production` file in the PHP installation directory to `php.ini`.
2. Open `php.ini` in a text editor.
3. Uncomment the following lines by removing the semicolon (`;`) at the beginning of each line:

    ```ini
    extension=mysqli
    extension=zip
    ```

4. Save the changes and close the file.

## Step 3: Add PHP to System Path

1. Open the System Properties window by pressing `Win + Pause/Break` or right-clicking on `This PC` and selecting `Properties`.
2. Click on `Advanced system settings` on the left-hand side.
3. In the System Properties window, click on the `Environment Variables` button.
4. In the `System variables` section, select the `Path` variable and click on the `Edit` button.
5. Click on the `New` button and enter the path to your PHP installation directory (e.g., `C:\php`).
6. Click `OK` to save the changes.

## Step 4: Test PHP Installation

1. Open a command prompt by pressing `Win + R`, typing `cmd`, and pressing `Enter`.
2. Type `php -v` and press `Enter`.
3. You should see the PHP version information if the installation was successful.

## Step 5: Install Composer

1. Go to the [Composer](https://getcomposer.org/download/) download page.
2. Download and run the Composer-Setup.exe installer.
3. Follow the installation wizard instructions.
4. Select the option to install Composer globally.
5. Once the installation is complete, open a new command prompt and type `composer --version` to verify the installation.

Congratulations! You have successfully installed PHP with the mysqli and zip modules, as well as Composer, on your Windows machine.

## Additional Resources

- [PHP Manual](https://www.php.net/manual/en/install.windows.php)
- [Composer Documentation](https://getcomposer.org/doc/)