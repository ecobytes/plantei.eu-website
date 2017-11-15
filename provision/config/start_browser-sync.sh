#!/bin/bash
LOGFILE="/tmp/log.log"
echo "1. Running Command $(ps ax | grep screen)" >> "${LOGFILE}"
if [ -z "$STY" ]; then
  exec screen -dm -S brower-sync /bin/bash "$0"
  echo "2. STARTED SCREEN $(ps ax | grep screen)" >> "${LOGFILE}"
fi

echo "3. AFTER IF" >> "${LOGFILE}"

PIDFILE="/tmp/browser-sync.pid"

if [ "$1" == "-r" ]; then
   THATPID="$(cat $PIDFILE 2>/dev/null)"
   if [ ! -z $THATPID ]; then
     kill $THATPID 2>/dev/null
   fi
fi

echo $$ > "$PIDFILE"
echo "4. before NVM $(ps ax | grep screen)" >> "${LOGFILE}"
export NVM_DIR="/home/vagrant/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm

echo "5. AFTER GULP $(ps ax | grep screen)" >> "${LOGFILE}"
while [ -z "$(mount | grep vagrant)" ]; do
  sleep 1
done

cd /vagrant

gulp
