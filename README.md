# DBHomekit
Flamigo switches from homewizard (sold by ACTION stores) to control with IFTTT (e.g. Google Assistent)

## Getting started
Download the archive and extract. Upload to your website in a folder name e.g. "home" or copy to your local machine running a webserver

### Prerequisites
- PHP 5.3 or higher
- Curl
- Your credentials used to register with the HomeWizard Lite app that controls your switches (the HomeWizard Lite app itself is not needed)

### Installing
This is pretty straight forward. After copying to your website navigate to the folder you have installed DBHomekit to.
An installation wizard will guide you through the process with a few questions.
1. Asking for a username and password to secure the DBHomekit from access by others.
2. Asking your credentials you used to register the HomeWizard Lite app that controls your switches.
3. DBHomeKit will then make a connection to HomeWizard to retrieve all your switch information.

## Usage
The main page is the dashboard with an overview of all your switches, including the SmartSwitch itself, as separate cards.
The SmartSwitch has a card with a blue background.

''This will only show up if you have used the HomeWizard Lite app to link and setup your switches.''

Every switch can be assigned a room and a type. You can create new rooms and types from the menu above.
As soon as you have created a type or room you can actually assign them to a switch (don't forget to press the save button after changes).

Every switch can be turned on or off with the icon at the top of each card.

### IFTTT
Install the IFTTT app on your phone and make an account.

Create a new applet (either in the app or their website).
For THIS you can choose Google Assistant. For THAT choose the WEB service. 
From the menu IFTTT in DBHomekit you can find what you need to fill in at the WEB service of IFTTT.

Have fun!

#### Refetch Database
If you have made any changes in the HomeWizard Lite app (renaming switches or added/deleted switches), just click on the link in the menu "Refetch Database" and DBHomekit will update you devices.

## Authors
Designburo.nl

## Version
* 1.93 Added support for dutch on / off as an action
* 1.92 Small style enhancements
* 1.91 Small enhancements
* 1.9  Added icons
* 1.8  Getting rid of some debug messages
* 1.7  Added Url support (GET) instead of only POST
* 1.6  Added toggle to device cards to keep things compact
* 1.5  Bit of code cleanup
* 1.4  Finished IFTTT Wizards
* 1.3  Corrected file headers
* 1.2  Installation improvements
* 1.1  Fixed light on/off buttons in panels
* 1.0  Initial release
