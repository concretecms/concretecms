mtail_nginx() {
  multitail /var/log/nginx/access.log -I /var/log/nginx/error.log;
}

tail_nginx() {
  grc -c "$GITPOD_REPO_ROOT/.gp/conf/grc/nginx-log.conf" \
  tail -f /var/log/nginx/access.log /var/log/nginx/error.log
}

start_nginx() {
  local fpm=
  fpm="/usr/sbin/php-fpm$(bash .gp/bash/utils.sh php_version)"
  nginx & "$fpm" --fpm-config .gp/conf/php-fpm/php-fpm.conf
  local exit_code=$?
  (( exit_code == 0 )) || return
  local log_monitor_type=
  log_monitor_type=tail
  case $log_monitor_type in
    'tail')
    tail_nginx
    ;;
    'multitail')
    mtail_nginx
    ;;
    *)
    echo "ERROR start_nginx: invalid nginx_log_monitor type: $log_monitor_type."
    ;;
  esac
}

stop_nginx() {
  nginx -s stop && pkill "php-fpm$(bash .gp/bash/utils.sh php_version)"
  local exit_code=$?
  (( exit_code == 0 )) || return
  local pid
  local log_monitor_type
  log_monitor_type=tail
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
    echo "ERROR stop_enginx: invalid nginx_log_monitor type: $log_monitor_type."
    ;;
  esac
  [[ -n $pid ]] && kill -2 "$pid"
}
