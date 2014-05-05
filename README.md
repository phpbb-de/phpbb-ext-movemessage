# phpBB 3.1 Extension - Move Message

## Installation

Clone into phpBB/ext/phpbbde/movemessage:

    git clone https://github.com/phpbb-de/phpbb-ext-movemessage.git phpBB/ext/phpbbde/movemessage

Go to "ACP" > "Customise" > "Extensions" and enable the "phpBB.de - Move Message" extension.

## Tests and Continuous Integration

We use Travis-CI as a continous integration server and phpunit for our unit testing. See more information on the [phpBB development wiki](https://wiki.phpbb.com/Unit_Tests).
To run the tests locally, you need to install phpBB from its Git repository. Afterwards run the following command from the phpBB Git repository's root:

Windows:

    phpBB\vendor\bin\phpunit.bat -c phpBB\ext\phpbbde\movemessage\phpunit.xml.dist

others:

    phpBB/vendor/bin/phpunit -c phpBB/ext/phpbbde/movemessage/phpunit.xml.dist

[![Build Status](https://travis-ci.org/phpbb-de/phpbb-ext-movemessage.svg?branch=develop-ascraeus)](https://travis-ci.org/phpbb-de/phpbb-ext-movemessage)

## License

[GPLv2](license.txt)
