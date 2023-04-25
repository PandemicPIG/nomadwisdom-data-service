#!/usr/bin/sh

if [ "$1" = "be" ]; then
  cd /var/project/nomadwisdom-data-service
  git fetch
  git pull
  git checkout master
  cp -r /var/project/nomadwisdom-data-service/* /var/www/html/
  echo "BE job done"
else
    if [ "$1" = "fe" ]; then
      cd /var/project/nomadwisdom
      git fetch
      git pull
      git checkout main
      npm run build
      cp -r /var/project/nomadwisdom/dist/* /var/www/html/
      echo "FE job done"
    else
      echo "nothing to deploy"
    fi
fi
