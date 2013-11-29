twitter-bootstrap-zf1
=====================

A library to work with the Twitter Bootstrap and Zend Framework 1. 

* Twitter Bootstrap itself is not included as a dependency. To import it into your project, you can use this code: https://gist.github.com/4002913
* You need a copy of ZF1 as well. This project used simukti/zf1 for testing.

This library is based on Easybib by Michael Scholl (https://github.com/easybib/EasyBib_Form_Decorator).

Installation
------------


```json
{
    /* Your code here ... */
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "bonndan/twitter-bootstrap-zf1",
		"version": "dev-master",
                "source": {
                    "url": "https://github.com/bonndan/twitter-bootstrap-zf1",
                    "type": "git",
		    "reference": "master"
                }
            }
        }
    ],
    "require": {
            /* Your code here ... */
            "bonndan/twitter-bootstrap-zf1: "*"
    }
}
```



License
-------
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

