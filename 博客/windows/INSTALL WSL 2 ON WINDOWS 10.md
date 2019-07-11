# [INSTALL WSL 2 ON WINDOWS 10](https://www.thomasmaurer.ch/2019/06/install-wsl-2-on-windows-10/)

With the Windows 10 Insider Preview Build 18917, the team also ships the first version of the Windows Subsystem for Linux 2 (WSL 2), which was announced at the Microsoft Build 2019 conference. In this post, I am going to show you how you can install WSL 2 on your Windows 10 machine.

The Windows Subsystem for Linux (WSL 1) was in Windows 10 for a while now and allowed you to use different Linux distros directly from your Windows 10 machine. With WSL 2, the architecture will change drastically and will bring increased file system performance and full system call compatibility. WSL 2 is now using virtualization technology (based on Hyper-V) and uses a lightweight utility VM on a real Linux kernel. You can find out more about WSL 2 in the release blog or on the Microsoft Docs Page for WSL 2.

WSL 2 Architecture

Requirements
To install WSL 2, you will need the following requirements:

Windows 10 Insider Preview Build 18917 or higher
A computer that supports Hyper-V Virtualization
Install WSL 2
To install the Windows Subsystem for Linux 2 (WSL 2), you need to follow these tasks.

Enable the Windows Subsystem for Linux Optional feature (WSL 1 and WSL 2)
Install a distro for the Windows Subsystem for Linux
Enable the ‘Virtual Machine Platform’ optional feature (WSL 2)
Configure the distro to use WSL 2
Enable the Windows Subsystem for Linux
To run the WSL on Windows 10 you will need to install the optional feature:

Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux
Install a Linux distro for the Windows Subsystem for Linux
If you don’t already have installed a WSL distro, you can download and install it from the Windows 10 store. You can find more here: Crazy times – You can now run Linux on Windows 10 from the Windows Store

Enable the Virtual Machine Platform feature
WSL 2 Enable Virtual Machine Platform
WSL 2 Enable Virtual Machine Platform

To make use of the virtualization feature for WSL 2, you will need to enable the optional Windows feature. You can run the following PowerShell command to do this. You will need to start PowerShell as an Administrator. After you run this command, you might need a restart of your computer.

Enable-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform
Set WSL distro to use version 2
After you completed the first two steps, you will need to configure the distro to use WSL 2. Run the following command to list the available distros in PowerShell:

wsl -l -v
To set a distro to WSL 2 you can run the following command:

wsl --set-version DistroName 2
You can also set WSL 2 as the default:

wsl --set-default-version 2
To find out more about installing WSL 2, check out the Microsoft Docs page.

If you are now running your distro using WSL 2, you can now see that there is a Virtual Machine worker process running and if you search a little bit more, you can also find the VHDX file of the distro.

WSL 2 VHDX file

I hope this helps you and gives you a quick overview, if you have any questions, let me know in the comments and check out the WSL 2 FAQ. The Windows Subsystem for Linux 2 Kernel is also open-source, you can follow the [project on GitHub](https://github.com/microsoft/WSL2-Linux-Kernel).