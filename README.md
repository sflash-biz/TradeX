## About and Links

**STrade** - free open-source traffic trade script. What is mean "traffic trade script" you can [read here](http://sflash.biz/#solutions).


Original repo: https://github.com/jmbsoft/TradeX


Unofficial forums and support: http://www.unofficialjmbsupport.com/  


My adult webmasters page: http://sflash.biz/


Online Docs: http://sflash.biz/sft-dist-pub/docs/


Contribute and contact if you need domain keys for automatically script installation. Or use simple manual installation [described below](#manual-installation).


Sorry, but original TradeX script is morally obsolete and was not ready for production (if you try it use from original repo). TradeX have lot errors and deprecations, and most importantly, it have no outgoing list traffic algorithms. In my case script works with 400k site without troubles and with 50+ another smaller or similar sites on same server using classic trade script outlist with some additional improvements.


## My Fork Role:

- Redesigned general Stats. On top of stats page You can see some important Overall stats.
- Adequate "Outgoing List" with sort by trader ratio (ratio formula can be found in the documentation).
- Network working improvements.
- More necessary system trades. Including Spiders (Bots) and Spiders list edit interface.
- Possibility to use Redis as local storage for fast stats (script dont use any DB engine, it not needed).
- Improved interface for Hourly forces.
- Added Network trade deletion.
- Trader GEO stats visualisation improvements (removed Adobe Flash GUI).
- Fix lots of bugs.
- All FREE! Script dont get any hit of traffic for me. (Just do not forget remove my urls added as default in General Settings and from System Traders!)
- Use as is.
- Updates and some screenshots you can found here: https://forums.unofficialjmbsupport.com/index.php?topic=381.0


## Installation (Go to Manual Installation manual if dont want contact me)

- Installation script located in install-script directory. (In this case you need have a key for domain, coz you need use my build from my servers. Try to convince me why I need it! But I'm glad to any script improvements, except typos.)
- Html docs located now only on my server http://sflash.biz/sft-dist-pub/docs/ (sorry, but but this is most easy way to be updated)


## Manual Installation

- Download script from this repo using Code -> **Download ZIP** and unpack ZIP archive.
- Copy and raname **sft** dir to your domain root dir. You can rename it as you want.
- Make files and dirs rights like in docs Installation manual or cd to your **sft** dir and run inside this commands:
```
find ./data/ -type d -exec chmod 0777 {} +
find ./data/ -type f -exec chmod 0666 {} +
find ./templates/ -type d -exec chmod 0777 {} +
find ./templates/ -type f -exec chmod 0666 {} +
chmod -R 777 logs/
chmod 666 image.php in.php out.php lib/config.php
```
- Next go to **sft**/lib/config.php. Open it with any text editor then find line:
```
$C['out_path'] = '/var/www/html/sft/out.php';
```
and change it to:
```
$C['out_path'] = '/YOUR_PATH_TO_WWW_DIR/sft/out.php';
```
and save file.
- Go to **http://domain.com/sft/cp/** login with default __admin__ / __123456__
- Go to top menu Settings -> Global Settings and change all domain name settings and path's to the corresponding to your site and server. BTW you can do same in lib/config.php with text editor.
- Done!

