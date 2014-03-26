#!/usr/bin/env python

import os

os.system("rm TestResult.log")
os.system("touch TestResult.log")

os.system("phpunit testDoodleTable.php >> TestResult.log")
os.system("phpunit testElement.php >> TestResult.log")
os.system("phpunit testFormHandler.php >> TestResult.log")
os.system("phpunit testDataHandler.php >> TestResult.log")
os.system("phpunit testEwigerChat.php >> TestResult.log")

os.system("cat TestResult.log")

os.system("echo '======================='")
os.system("grep FAILURES! TestResult.log")
os.system("grep OK TestResult.log")

