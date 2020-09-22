# -*- coding: utf-8 -*-
import sys
from tasks import test

if __name__ == '__main__':
    print(test.test.delay(1, 2))
    print(repr(sys.path))
