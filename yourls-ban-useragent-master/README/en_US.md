# BlackList User-Agent [![Listed in Awesome YOURLS!](https://img.shields.io/badge/Awesome-YOURLS-C5A3BE)](https://github.com/YOURLS/awesome-yourls/)
- Requires [YOURLS](https://yourls.org) `1.91` and above.
- Select language: [:cn:](.//zh_CN.md) / [:us:](.//en_US.md) 
- This Page's Language is：:us: [English(US)](./en_US.md)
- ---
## Feature
:bulb: description: Disable access to short addresses in some browsers and prompt to open them in other browsers<br>
Some great functional designs based on the [this project](https://github.com/8Mi-Tech/short-url-mini-cn)

---
## Installation
1. In `/user/plugins`, create a new folder named `yourls-ban-useragent`. (Or you can use `git clone`)
2. Drop these files in that directory.
3. Go to the Plugins administration page (eg. `http://sho.rt/admin/plugins.php`) and activate the plugin.
4. Have fun!

---
## To Do List
Status Tags：:x: Not Solve / :o: Solved / :question: Unknown
| Status | Question |  Solved? | Note |
|-|-| :-: |-|
| :question: | identify User-Agent | [c41edc8](https://github.com/8Mi-Tech/yourls-ban-useragent/commit/c41edc8749f1fb11020187c714881177e68825ad) | Only detect WeChat/QQ User-Agent,<br> no support for custom UserAgent|
| :o: | Pages recognized and to be jumped to | [pls-use-other-ua.php](../pls-use-othher-ua.php) |
| :o: | Support [URL Ads](https://github.com/8Mi-Tech/yourls-conditional-urlads) Plugin | [c41edc8](https://github.com/8Mi-Tech/yourls-ban-useragent/commit/c41edc8749f1fb11020187c714881177e68825ad) | Plugin is too strong,<br>direct takeover,<br>no need to modify URL Ads plugin |
| :x: | Add in the administration page<br>Custom Blacklist User-Agent<br>Rule-List System、<br>and Hook by other plugins |  | To add to the admin page<br>1. Rules list<br>2.Custom rule list

---
## License

:bulb: The license might be updated with your terms.

This package is licensed under the [GPLv3](../LICENSE).
