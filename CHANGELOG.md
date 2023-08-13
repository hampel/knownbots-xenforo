CHANGELOG
=========

4.0.0 (2023-08-14)
------------------

* randomize cron time when installing to prevent API server being flooded
* bugfix: don't write initial knownbots.json directly to internal-data - we'll get file integrity errors when we update 
  it; just put it in the addon root and we'll copy it to internal-data during setup
* show knownbots.json build date in meaningful string format in CLI tools

4.0.0 beta 1 (2022-01-08)
-------------------------

* completely new build - bots are no longer hard coded, but updated via API calls and uses the XF code cache to store
  bot data
* raw bot data downloaded from API is stored in internal_data/knownbots.json  
* new CLI tool for manually fetching bots from API (Cron task is also provided)
* new CLI tool for manually loading bots from knownbots.json
* new CLI tool for testing user agent matches

3.20.0 (2021-07-01)
-------------------

* new bots:
    * btbot: [BT Bot](http://www.btbot.com/btbot.html)
    * catchbot: [Catchbot](http://www.catchbot.com)
    * comodospider: [Comodo SSL Spider](https://ssl.comodo.com/)
    * deepnoc: [deepnoc bot (network optimized crawling)](https://deepnoc.com/bot)
    * dispenserbot: [Dispenser Dab Solver Checklinks Bot](http://dispenser.info.tm/~dispenser/view/checklinks)
    * epicbot: [Epictions EpicBot](http://www.epictions.com/epicbot)
    * esperanzabot: [EsperanzaBot](http://www.esperanza.to/bot/)
    * fast enterprise crawler: [Fast enterprise crawler 6 used by Schibsted](mailto:webcrawl@schibstedsok.no)
    * fleabot: [Mercadopar Fleabot](https://mercadopar.com/fleabot)
    * fuseonbot: [Fuseon Link Affinity Bot](http://linkaffinity.io)
    * google/bot: [google/bot]()
    * greenflare seo crawler: [Greenflare SEO Crawler](https://greenflare.io/)
    * gsitecrawler: [GSiteCrawler](http://gsitecrawler.com/)
    * holmes: [Morfeo Holmes Bot](http://morfeo.centrum.cz/bot)
    * infotigerbot: [InfoTiger Search Engine Bot](https://infotiger.com/bot)
    * internet security survey bot: [Internet Security Survey Bot]()
    * jetpack-bot: [JetPack Bot]()
    * mojoo robot: [Mojoo Bot](http://www.mojoo.com/)
    * nicecrawler: [NiceCrawler](http://www.nicecrawler.com/)
    * prem.moe crawler: [Prem.moe Crawler](https://prem.moe/)
    * sayindexbot: [SayIndex Bot]()
    * sbl-bot: [SoftByte Labs Bot](http://sbl.net)
    * siteliner: [Siteliner Bot](http://www.siteliner.com/bot)
    * swjschecketbot: [Swjschecketbot]()
    * trade desk ads.txt & sellers.json crawler: [Trade Desk ads.txt & sellers.json crawler]()
    * vortex: [Marty Anstey Vortex Bot](http://marty.anstey.ca/robots/vortex/)
    * www.hlabs.co.ke: [hLabs Bot](https://www.hlabs.co.ke/bot.html)
    * zspider: [Red Kolibri ZSpider](http://feedback.redkolibri.com/)

3.19.0 (2021-05-31)
-------------------

* new false positives:
    * spider v9 build/mra58k
    
* new bots:
    * 80legs.com: [80legs Crawler](https://80legs.com/)
    * adssellerscrawler: [Ads Sellers Crawler](https://www.protected.media/)
    * apesearch crawler: [ApeSearch Crawler](mailto:xiongrob@umich.edu)
    * aportcatalogrobot: [AportCatalogRobot]()
    * bot dns-cache.fr: [bot dns-cache.fr]()
    * crusty broad web crawler: [Crusty Broad Web Crawler](https://lib.rs/crates/crusty)
    * docomo: [NTT Docomo Goo Bot](http://help.goo.ne.jp/door/crawler.html)
    * dubbotbot: [DubBot Bot](https://dubbot.com/)
    * electricmonk: [DueDil Electric Monk Crawler](https://www.duedil.com/our-crawler/)
    * finbot: [Finbot]()
    * flok's crawler: [Flok's Crawler](https://flok.codes)
    * goodbot: [GoodBot]()
    * grover: [Grover web crawler]()
    * joc web spider: [JOC Web Spider](http://www.jocsoft.com/jws/)
    * linkarchiver twitter bot: [LinkArchiver Twitter Bot](https://github.com/thisisparker/linkarchiver)
    * linkscrawler: [LinksCrawler]()
    * neevabot: [Neevabot Search Engine Bot](https://neeva.com/neevabot)
    * newsharecounts.com: [Newshare Counts](http://newsharecounts.com/crawler)
    * newslitbot: [Newslit Bot](https://www.newslit.co)
    * opensearch@mpdl: [OpenSearch@MPDL](https://www.mpdl.mpg.de/opensearch_crawler_faq.html)
    * orbbot: [orbbot]()
    * pajbot1: [Pajbot](https://pajbot.com/)
    * richaudience brandsafety bot: [Rich Audience Brandsafety Bot](https://richaudience.com/en/advertisers/)
    * semjibot: [SemjiBot](http://semji.com)
    * simplecrawler/0.1: [SimpleCrawler]()
    * snap url preview service: [Snap URL Preview Service](https://developer.snapchat.com/robots)
    * spider_bot/3.0: [Spider_bot]()
    * spotibobot: [Spotibo bot](http://spotibo.com)
    * synologychatbot: [Synology Chat Bot](https://www.synology.com/en-global/knowledgebase/DSM/help/Chat/chat_integration)
    * terrawizbot: [TerrawizBot]()
    * top100.rambler.ru crawler: [Top-100 Rambler Crawler](https://top100.rambler.ru/)
    * webshopchecker bot: [Webshop Checker Bot](https://webshopchecker.nl)
    * wi job roboter spider: [Web Integration Job Robot](https://www.webintegration.at)
    * woobot: [WooBot]()
    * woriobot: [Zite.com WorioBot](http://zite.com)
    * wp fastest cache preload bot: [WP Fastest Cache Preload Bot](https://www.wpfastestcache.com/features/preload-settings/)
    * xenforo: [XenForo](https://xenforo.com/)
    * your bot: [Your Bot]()
    * yunsecuritybot: [YunSecurityBot]()
    * yurichevbot: [Yurichev Bot](http://yurichev.com/bot.html)
                 

3.18.0 (2021-04-24)
-------------------

* new false positives: 
    * cubot; j5
    * baiduboxapp

* new bots:
    * 200pleasebot
    * a8bot
    * abilogicbot
    * acoonbot
    * adform robot
    * arhpostbot
    * atomseobot
    * awariorendererbot
    * badoobot
    * bl.uk_ldfc_bot
    * brobot
    * charityengine bot
    * charlotte
    * cosmos
    * coveobot
    * crawlbot/1.0.0
    * cxensebot
    * facebot
    * fandomopengraphbot
    * freshpingbot
    * fuelbot
    * geedobot
    * getlocalbot
    * google-safety
    * gpcsupbot
    * grub-client
    * gynxbot
    * healrworld crawler
    * hgfalphaxcrawl
    * hoodle crawler
    * idmarch automatic
    * imrbot
    * jambot
    * justlocal.nl
    * kantarsifomediaauditbot
    * keobsbot
    * keybasebot
    * koepabot
    * lanaibot
    * landsbokasafn
    * lapozzbot
    * linkpulse metacrawler
    * linksmanager.com_bot
    * lxrbot
    * mbot v
    * moreoverbot
    * netpeakspiderbot
    * www.niraiya.com
    * node/simplecrawler
    * nu.marginalia.wmsa.edge-crawler
    * nutchcvs
    * oer commons bot
    * omniexplorer_bot
    * onefuncbot
    * oozbot
    * pickybot
    * piepmatz bot
    * plukkie
    * pu_in crawler
    * punkspider
    * pwa-crawler
    * reasonalbot
    * revuebot
    * runet-research-crawler
    * screenerbot crawler
    * searchenginecrawler
    * sebot-wa
    * seekbot
    * shopwiki
    * showyoubot
    * siteauditbot
    * sitescorebot
    * spinn3r
    * squirrobot
    * ssl-crawler
    * thinkbot
    * tsmbot
    * tweetedtimes bot
    * ucrawl
    * umichbot
    * urlappendbot
    * verticalleap-sitestatusbot
    * webgraph
    * weblinkchecker
    * websquash.com
    * wellknownbot
    * wizenozebot

3.17.0 (2021-01-25)
-------------------

* new false positives: 
    * cubot r11
    * spider v7 build/lmy47i
    * spider v7 (MyCell Spider v7 from Bangladesh)

* weekly new bots:
    * adbot/1.0
    * ahrefssiteaudit
    * amazonadbot
    * amg-bot
    * ampxfbot
    * anybot
    * aranea web-crawled corpora project
    * backlinkcrawler
    * botelaire
    * chirpyhubbot
    * cookiebot
    * crawl/1.0
    * dataforseobot
    * diffeobot
    * digitalshadowsbot
    * discoverbot
    * dmasslinksafetybot
    * domcopbot
    * echocrawl
    * emeraldshield.com webbot
    * historyspider
    * iplogger crawler
    * mohawk crawler
    * mtrobot
    * mxbot
    * netresearchserver
    * openindexspider
    * pinllc search robot
    * rustbot
    * sellers.guide crawler by primis
    * snaplocalbot
    * test-deep-cocrawler
    * vdo.ai bot
    * wesee:search
    * whizebot
    * xaldon_webspider
    * xovibot
    * yaanibot
    * yelpbot           

3.16.0 (2020-12-29)
-------------------

* weekly new bots:
    * boitho.com-dc
    * msc crawl project radboud university
    * niocbot
    * open web analytics bot
    * quetextbot
    * rc-crawler
    * tokenspider
    * womlpefactory
    * yeti/1.0

3.15.0 (2020-12-21)
-------------------

* new Dinobot Android TV false positives:
    * dinobot 4k plus

* weekly new bots:
    * cis455crawler
    * crystalsemanticsbot
    * discoverspider
    * envolk[its]spider
    * geograph linkcheck bot
    * gg peekbot
    * iccrawler
    * mybot
    * psbot
    * suggybot
    * testbot

3.14.0 (2020-12-07)
-------------------

* weekly new bots:
    * antbot/1.0
    * becomebot
    * castlebot
    * custom-crawler
    * feedsearch bot
    * gulper web bot
    * istellabot
    * lmspider
    * marketingminer bot
    * my nutch spider
    * networking4all bot
    * nfwebcrawler
    * tombapublicwebcrawler
    * uptimebot
    * vuhuvbot
    * webcrawl.net
    * webspider 1.0

3.13.0 (2020-12-02)
-------------------

* new Cubot phone false positives:
	* cubot a5
	* cubot j3 pro
	
* weekly new bots:
    * addsugarspiderbot
    * annuairefrancais.fr
    * coibotparser
    * criteobot
    * discobot
    * domainspider-bot
    * dropboxpreviewbot
    * dumbot
    * gnowitnewsbot
    * hoaxybot
    * ichiro
    * lamarkbot
    * media-bot
    * nusearch spider
    * radian6_default_
    * xovionpagecrawler
    * zyborg              

3.12.0 (2020-11-17)
-------------------

* weekly new bots:
    * experiancrawluk
    * impact radius compliance bot
    * kauaibot
    * leuchtfeuer crawler
    * redirectbot
    * simpleanalyticsbot
    * skimbot
    * sogou pic spider
    * testcrawler

3.11.0 (2020-11-09)
-------------------

* weekly new bots:
    * girafabot
    * googebot malware scanning
    * i-market-bot
    * intelx.io_bot
    * ninjabot
    * pywebcopybot
    * serpstatbot
    * supremesearch.net
    * todoexpertosbot
    * webmoney megastock robot
    * wordchampbot
    * xaldon webspider
    * yesslebot

3.10.0 (2020-11-02)
-------------------

* weekly new bots:
    * aasa-bot
    * amazon-advertising-ad-standards-bot
    * crawler/0.0.1
    * danibot
    * dragonbot
    * irlbot
    * jooblebot
    * ldspider
    * linkisbot
    * mohawk-crawler
    * newsgator
    * ottobot
    * pigafetta
    * pingdom.com_bot
    * planckspider
    * rss bot
    * speedy spider
    * summalybot
    * virusdie crawler                   
    
3.9.0 (2020-10-26)
------------------

* weekly new bots:
    * earwigbot
    * fess
    * metadata-downloader-bot
    * page audit bot
    * plurkbot
    * r6_commentreader
    * r6_feedfetcher
    * sc_bot
    * tkbot
    * willie irc bot        

3.8.0 (2020-10-20)
------------------

* weekly new bots:
    * ag_dm_spider
    * aihitbot
    * avocetcrawler
    * b2b bot
    * checkbot
    * em-crawler
    * feedsearch-crawler
    * fullstorybot
    * krzana-rss-bot
    * mediacloud bot for open academic research
    * mlbot
    * niuebot
    * ocarinabot
    * prft-bot
    * qwantbot
    * rasabot         
    * riverbot
    * rytebot
    * sabsimbot
    * scribbr-citation-bot
    * seodiver
    * snapbot
    * solomonobot
    * temeliobot-keyword-scrapper    
    * vsusearchspider
    * your-search-bot 

3.7.0 (2020-10-06)
------------------

* new Cubot phone false positives:
	* cubot dinosaur
* new GLX phone false positives:
	* GLX Spideri
* weekly new bots:
	* ant.com beta
	* bha2r_bot
	* browserspybot
	* c-t bot
	* cms crawler
	* ioncrawl
	* js-crawler
	* k7mlwcbot
	* quora-bot
	* qwarrycrawler
	* sidetrade indexer bot
	* webzip       

3.6.0 (2020-09-28)
------------------

* new Cubot phone false positives:
	* cubot cheetah 2
* weekly new bots:
	* better uptime bot 
	* btcrawler 
	* crawler_eb_germany_2.0 
	* dle_spider.exe 
	* dyno mapper crawler 
	* flockbrain robot 
	* infoobot 
	* microadbot 
	* nesotebot 
	* reachabilitycheckbot 
	* se ranking gentle bot 
	* seobilitybot 
	* siteguru linkchecker 
	* statonlinerubot 
	* tombot 
	* viulinkcrawler 
	* webgains-bot'
	* _zbot                                                                                                                                                                                                 

3.5.0 (2020-09-21)
------------------

* new Cubot phone false positives:
	* cubot magic
	* cubot_manito
	* cubot_power
* simplify seokicks-robot match to just seokicks to catch new bot user agent
* weekly new bots:
	* applenewsbot
	* bl.uk_lddc_bot
	* dcrawl
	* dy robot
	* ezlynx
	* fast-webcrawler
	* gdark-spider
	* gethpinfo.com-bot
	* gowikibot
	* image size by siteimprove.com
	* linkcheck by siteimprove.com
	* loaderio;verification-bot
	* pingdompagespeed
	* pmoz.info odp link checker
	* refindbot
	* seebot.org
	* siteanalyzerbot
	* sitecheck-sitecrawl
	* sottopop
	* superbot
	* tineye-bot
	* webliobot	            
                                                                                                                                                                        	

3.4.0 (2020-09-14)
------------------

* update: email subject line now includes addon version
* feature: now optionally uses Monolog Logging Service addon for logging info about sent emails
* fixed missing mailto: in some email links
* new Cubot phone false positives:
	* cubot h3
	* cubot_note_s build/lmy47i
* weekly new bots:
	* 3dd trunk
	* 3w24bot
	* auto spider
	* aylien
	* bidswitchbot
	* bnf.fr_bot
	* bublupbot
	* checkmarknetwork
	* chimebot
	* cloudservermarketspider
	* curious george
	* diffbot
    * everyfeed-spider	
	* ffzbot
	* finditanswersbot
	* fyrebot
	* gigabot 
	* graydon bot
	* hrankbot
	* huaweiwebcatbot
	* hypestat
	* hyscore 
	* implisensebot
	* jasper's lil' bot'
	* jetslide
	* jpg-newsbot
	* konturbot
	* letsearchbot
	* looid.com crawler
	* magibot
	* mauibot            
	* mindupbot
	* miralinks robot                                                                                                          
	* onalyticabot
	* online-webceo-bot
	* queryseekerspider
	* randomsurfer
	* rankurbot
	* researchbot                                                                                                           
	* safednsbot
	* serptimizerbot
	* sitecheckerbotcrawler
	* somdsearchbot
	* ssblog rsscrawler
	* sserobots
	* statdom.ru
	* synthesio crawler
	* toutiaospider
	* turnitinbot
	* website-audit.be crawler
	* who.is bot
	* wikido
	* wlc pywikibot
	* x28-job-bot
	* xyz spider
	* y!j-asr
	* yetibot
	* zombiebot

3.3.0 (2020-09-07)
------------------

* added a phrase for email subject instead of hardcoding the string
* removed some unused phrases
* removed Dalvik from list of bots - it's an Android browser
* fixed missing user agent string for rssingbot
* new Cubot phone false positives
	* cubot echo
	* cubot_j3
	* cubot king kong
	* cubot max
	* cubot note plus
	* cubot_note_s
	* cubot_note_s build/mra58k
	* cubot_nova
	* cubot r9
	* cubot_x18_plus	
* lots of new bots
	* adstxtlab.com
	* anderspinkbot
	* arquivo-web-crawler
	* barkrowler
	* botw spider
	* buzzbot
	* centro ads.txt crawler
	* cispa webcrawler
	* claritybot
	* cognitiveseo.com
	* crowdtanglebot
	* dingtalkbot-linkservice
	* elmer, the thinglink imagebot
	* elisabot
	* grfzbot
	* hubspot crawler
	* hubspot url validation check
	* icc-crawler
	* jobboersebot
	* kingbot
	* knot group
	* lawinsiderbot
	* makemoneyteamworkbot
	* mastodon
	* maxpointcrawler
	* mediapartners-google
	* mediumbot-metatagfetcher
	* metajobbot
	* msiecrawler
	* neticle crawler
	* netseer crawler
	* nextcloud server crawler
	* ninjbot
	* pagething.com
	* pagepeeker
	* pandalytics
	* parsijoobot
	* pimeyes.com
	* popscreen bot
	* pulno
	* pulsepoint-ads.txt-crawler
	* ravencrawler
	* redditbot
	* rely bot
	* rssmicro.com
	* sbooksnet
	* scanmine newsspider
	* seo-audit-check-bot
	* seoclaritycrawl
	* sitelockspider
	* slack-imgproxy
	* slackbot-linkexpanding
	* snappreviewbot
	* spiderling
	* squidbot
	* stormcrawler
	* superfeedr bot
	* superpagesbot
	* tmmbot
	* trendkite-akashic-crawler
	* ucmore crawler app
	* vebidoobot
	* yellowbrandprotectionbot
	* vkrobot
	* voilabot
	* wiederfreibot
	* yacybot
	* zumbot                                                                                                                                                                                                                                                                                                                           	
	
3.2.0 (2020-09-01)
-------------------

* lots of new bots:
	* 360spider
	* adsbot
	* adstxtcrawler
	* awariorssbot
	* bitlybot
	* boardreader
	* ccbot
	* cincraw
	* clickagy intelligence bot
	* crawlson
	* crsspxlbot
	* datagnionbot
	* deskyobot
	* df bot
	* dnsresearchbot
	* duckduckgo favicons bot
	* eyeotabot
	* feedlybot
	* foocrawlerbot
	* germcrawler
	* google adwords instant
	* gumgum bot
	* hatena
	* hetrixtools
	* hubpages
	* ias_crawler
	* internet structure research project bot
	* jugendschutzprogramm-crawler
	* krzana bot
	* lightspeedsystemcrawler
	* linespider
	* livelapbot
	* lufsbot
	* moatbot
	* netestate ne crawler
	* netpeakchekerbot
	* netvibes
	* nimbostratus bot
	* nixstatsbot
	* obot
	* odklbot
	* paperlibot
	* pilicanbot
	* pleskbot
	* politecrawl
	* pubmatic crawler bot
	* rogerbot
	* scooperbot
	* screaming frog seo spider
	* semantic-visions.com
	* semanticbot
	* seolizer
	* sirdatabot
	* surdotlybot
	* tapatalk cloudsearch platform
	* tpradstxtcrawler
	* velenpublicwebcrawler
	* yisouspider
	* wp.com feedbot
	* zoombot
* added cubot smartphones to false positive list

3.1.0 (2020-08-27)
-------------------

* new bots (already!): amazonbot; petalbot; slackbot

3.0.0 (2020-08-27)
-------------------

* major update: generic bot detection
* caches list of new bots detected
* option to email list weekly
* minimum PHP version now 7.0.0
* new bots: AccompanyBot; PostmanRuntime

2.7.0 (2020-05-17)
-------------------

* lots of new bots:
	* bots from Google: https://support.google.com/webmasters/answer/1061943?hl=en
	* UptimeBot
* renamed a few ids and phrase strings
* new tool to detect bots in user agent strings

2.6.0 (2020-04-12)
-------------------

* new bots: AspiegelBot; internetnz; microsoft office; winhttp
* TelegramBot (thanks alsogamer_)

2.5.0 (2020-03-02)
-------------------

* added an option to disable the display of robot statistics in the sidebar widgets 

2.4.0 (2020-03-02)
-------------------

* new bots: 7siters; adscanner; brandverity; duckduckbot; e.ventures; exabot; httrack; just-crawling; pinterestbot; 
re-re studio; semanticscholarbot; symfony browserkit; tracemyfile; um-ln; v-bot; wget

2.3.0 (2019-11-18)
-------------------

* added curl as a robot
* show robots online in Members online and Online statistics widgets 

2.2.0 (2019-09-30)
-------------------

* merge new bot array with core array rather than clobbering it

2.1.0 (2019-09-30)
-------------------

* new bots added: ArchiveTeam; ArchiveBot; BrandVerity  
* added tool to admin area to show list of bots

2.0.0a (2018-08-12)
-------------------

* re-release with new addon_id

2.0.0 (2018-06-11)
------------------

* migrate to XF2

1.0.1 (2018-02-21)
------------------

* new bots
* new bots
* lots of new bots
* added function so we can get robot map for display in admin tools
* keys need to be lower case
* added crawler4j; linguee; linkpadbot; mixrankbot; nlnz_iaharvester; ouclicksbot; semrushbot; seokicks-robot; 
  sitesucker; statuscase/virusscanner; vegi bot; wotbox
* added BingLocalSearch
* added LTX71

1.0.0 (2016-07-11)
------------------

* first working version
