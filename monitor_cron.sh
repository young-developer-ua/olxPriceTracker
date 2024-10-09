#!/bin/bash

while true; do
    inotifywait -e modify /etc/cron.d/cronjobs

    crontab /etc/cron.d/cronjobs
    echo "Cron jobs updated at $(date)" >> /var/log/cron_update.log
done
