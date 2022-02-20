# shellcheck shell=bash

# override linter for parsing pid: pgrep is too simple for this case
# shellcheck disable=2009

mtail_apache() {
  multitail /var/log/apache2/access.log -I /var/log/apache2/error.log;
}

tail_apache() {
  grc -c "$GITPOD_REPO_ROOT/.gp/conf/grc/apache-log.conf" \
  tail -f /var/log/apache2/access.log /var/log/apache2/error.log
}

mtail_nginx() {
  multitail /var/log/nginx/access.log -I /var/log/nginx/error.log;
}

tail_nginx() {
  grc -c "$GITPOD_REPO_ROOT/.gp/conf/grc/nginx-log.conf" \
  tail -f /var/log/nginx/access.log /var/log/nginx/error.log
}

start_apache() {
  apachectl start
  local exit_code=$?
  (( exit_code == 0 )) || return
  local log_monitor_type=
  log_monitor_type="$(bash \
  "$GITPOD_REPO_ROOT"/.gp/bash/utils.sh parse_ini_value starter.ini development apache_log_monitor)"
  case $log_monitor_type in
    'tail')
    tail_apache
    ;;
    'multitail')
    mtail_apache
    ;;
    *)
    echo "ERROR start_apache: invalid apache_log_monitor type: $log_monitor_type. Check your starter.ini"
    ;;
  esac
}

stop_apache() {
  apachectl stop
  local exit_code=$?
  (( exit_code == 0 )) || return
  local pid
  local log_monitor_type
  log_monitor_type="$(bash \
  "$GITPOD_REPO_ROOT"/.gp/bash/utils.sh parse_ini_value starter.ini development apache_log_monitor)"
  case $log_monitor_type in
    'tail')
    # The grep string here must match the tail portion of the command given in the function tail_apache
    pid=$(ps axf  | grep 'tail -f /var/log/apache2/access.log /var/log/apache2/error.log' \
    | grep -v grep | awk '{print $1}' | sed 1q)
    ;;
    'multitail')
    # The grep string here must match the command given in the function mtail_apache
    pid=$(ps axf | grep 'multitail /var/log/apache2/access.log -I /var/log/apache2/error.log' \
    | grep -v grep | awk '{print $1}' | sed 1q)
    ;;
    *)
    echo "ERROR stop_apache: invalid apache_log_monitor type: $log_monitor_type. Check your starter.ini"
    ;;
  esac
  [[ -n $pid ]] && kill -2 "$pid"
}

start_nginx() {
  local fpm=
  fpm="/usr/sbin/php-fpm$(bash .gp/bash/utils.sh php_version)"
  nginx & "$fpm" --fpm-config .gp/conf/php-fpm/php-fpm.conf
  local exit_code=$?
  (( exit_code == 0 )) || return
  local log_monitor_type=
  log_monitor_type="$(bash \
  "$GITPOD_REPO_ROOT"/.gp/bash/utils.sh parse_ini_value starter.ini development nginx_log_monitor)"
  case $log_monitor_type in
    'tail')
    tail_nginx
    ;;
    'multitail')
    mtail_nginx
    ;;
    *)
    echo "ERROR start_nginx: invalid nginx_log_monitor type: $log_monitor_type. Check your starter.ini"
    ;;
  esac
}

stop_nginx() {
  nginx -s stop && pkill "php-fpm$(bash .gp/bash/utils.sh php_version)"
  local exit_code=$?
  (( exit_code == 0 )) || return
  local pid
  local log_monitor_type
  log_monitor_type="$(bash \
  "$GITPOD_REPO_ROOT"/.gp/bash/utils.sh parse_ini_value starter.ini development nginx_log_monitor)"
  case $log_monitor_type in
    'tail')
    # The grep string here must match the tail portion of the command given in the function tail_apache
    pid=$(ps axf  | grep 'tail -f /var/log/nginx/access.log /var/log/nginx/error.log' \
    | grep -v grep | awk '{print $1}' | sed 1q)
    ;;
    'multitail')
    # The grep string here must match the command given in the function mtail_apache
    pid=$(ps axf | grep 'multitail /var/log/nginx/access.log -I /var/log/nginx/error.log' \
    | grep -v grep | awk '{print $1}' | sed 1q)
    ;;
    *)
    echo "ERROR stop_enginx: invalid nginx_log_monitor type: $log_monitor_type. Check your starter.ini"
    ;;
  esac
  [[ -n $pid ]] && kill -2 "$pid"
}

start_php_dev() {
  php -S 127.0.0.1:8000 -t public/
}

stop_php_dev() {
  local pid
  # The grep string here must match the command given in the function start_php_dev
  pid=$(ps axf | grep 'php -S 127.0.0.1:8000 -t public/' | grep -v grep | awk '{print $1}' | sed 1q)
  [[ -n $pid ]] && kill -2 "$pid"
}
