# -*- coding: utf-8 -*-
from celery import Celery
import os

# windows环境
os.environ.setdefault('FORKED_BY_MULTIPROCESSING', '1')

app = Celery('tasks')

app.config_from_object('tasks.conf')
