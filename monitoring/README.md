# Monitoring

The following monitoring and alerting stack relies on [Prometheus](https://prometheus.io/) and [Grafana](https://grafana.com/). It is installed and updated with [helm](https://helm.sh/).

## Table of contents
* [:chart_with_upwards_trend: Dashboards](#chart_with_upwards_trend-Dashboards)
* [:rotating_light: Alerting](#rotating_light-Alerting)
* [:beginner: Pods](#beginner-Pods)
* [:arrow_upper_right: Update configuration](#arrow_upper_right-Update-configuration)
* [:construction: Installation](#construction-Installation)

## :chart_with_upwards_trend: Dashboards

Prometheus offers different dashboards:
* The main prometheus dashboard, it can be accessed with port-forward at [localhost:9090](http://localhost:9090) after running:
  ```shell
  kubectl --namespace monitoring port-forward $(kubectl get pods --namespace monitoring -l "app=prometheus,component=server" -o jsonpath="{.items[0].metadata.name}") 9090
  ```
* The alertmanager dashboard, it can be accessed with port-forward at [localhost:9093](http://localhost:9093) after running:
  ```shell
  kubectl --namespace monitoring port-forward $(kubectl get pods --namespace monitoring -l "app=prometheus,component=alertmanager" -o jsonpath="{.items[0].metadata.name}") 9093
  ```
* The pushgateway dashboard, it can be accessed with port-forward at [localhost:9091](http://localhost:9091) after running:
  ```shell
  kubectl --namespace monitoring port-forward $(kubectl get pods --namespace monitoring -l "app=prometheus,component=pushgateway" -o jsonpath="{.items[0].metadata.name}") 9091
  ```
## :rotating_light: Alerting

## :beginner: Pods

The monitoring pods run in the `monitoring` namespace. To interact with them, use the regular `kubectl` commands with the `--namespace` (or `-n`) flag:
```shell
$ kubectl get pods --namespace monitoring
```

There are several monitoring pods:
* A main `prometheus-server` pod that aggregates all collected metrics.
* A `prometheus-node-exporter` on each node that collects node metrics and sends them to the `prometheus-server`.
* A `prometheus-alertmanager` pod that sends notifications based on metrics events (webhook, mail, slack, ...).
* A `prometheus-pushgateway` pod that collects metrics from Jobs and CronJobs.
* A `prometheus-kube-state-metrics` pod that collects cluster level metrics.

## :arrow_upper_right: Update configuration

## :construction: Installation
