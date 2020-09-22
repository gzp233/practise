# -*- coding: utf-8 -*-
import time
from tasks import app


@app.task
def test(x, y):
    time.sleep(3)
    return x + y
