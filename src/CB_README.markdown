Commerce Bug Installation Guide
==================================================
**Please Note** If you are installing Commerce Bug 2 over Commerce Bug 1, it is necessary to manually remove all files related to Commerce Bug 1.  If you require assistance, please contact Pulse Storm Support: http://www.pulsestorm.net/contact-us/


Commerce Bug 2 is distributed as a Magento Connect 2 extension.  You'll find it in the zip file you downloaded with an name something like

    Packagename_Commercebug-2.x-x.tgz
    
Where "Packagename" is the customized Magento package name for your extension.   

This package may be installed in a Magento system using the Magento Connect manager.  If the Magento connect manager is unavailable on your system, you can install the extension manually by un-taring the tgz file and following the remaining steps in the README file. 

1. Copy/Upload the contents of the "app" folder to the corresponding "app" folder of your Magento system

2. Copy/Upload the contents of the "js" folder to the corresponding "js" folder of your Magento system

3. Ensure the uploaded files and folders have *nix permissions, ownership and groups identical to the rest of your Magento files and folder

4. Clear the Magento Cache.  This may be done under [System -&gt; Cache Management]

5. Log out of the Magento Admin Application to clear you admin session.  This is necessary to grant admin super users the correct access permissions to view Commerce Bug's system configuration.

6. If you're using compilation, re-compile your classes.

7. Configure Commerce Bug (update notifications, output, etc.) at System -&gt; Configuration -&gt; Advanced -&gt; Commerce Bug