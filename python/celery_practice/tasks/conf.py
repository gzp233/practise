# -*- coding: utf-8 -*-
from datetime import timedelta
from celery.schedules import crontab

BROKER_URL = 'redis://localhost:6379/1'

CELERY_RESULT_BACKEND = 'redis://localhost:6379/2'

CELERY_TIMEZONE = 'Asia/Shanghai'

# 导入指定任务模块
CELERY_IMPORTS = (
    'tasks.test'
)

CELERYBEAT_SCHEDULE = {
    'task1': {
        'task': 'celery.test.test',
        'schedule': timedelta(seconds=10),
        'args': (2, 8)
    },
    'task2': {
        'task': 'celery.test.test',
        'schedule': crontab(hour=19, minute=28),
        'args': (3, 8)
    }
}
