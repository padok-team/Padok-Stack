# Monitoring

The following monitoring and alerting stack relies on [Prometheus](https://prometheus.io/) and [Grafana](https://grafana.com/). It is installed and updated with [helm](https://helm.sh/).

## Table of contents
* [:chart_with_upwards_trend: Dashboards](#chart_with_upwards_trend-Dashboards)
* [:rotating_light: Alerting](#rotating_light-Alerting)
* [:beginner: Pods](#beginner-Pods)
* [:arrow_upper_right: Update configuration](#arrow_upper_right-Update-configuration)
* [:construction: Installation](#construction-Installation)

## :chart_with_upwards_trend: Dashboards

Grafana has a main dashboard to visualize metrics collected by prometheus. It can be accessed with port-forward at [localhost:3000](http://localhost:3000) after running:
  ```shell
  $ kubectl --namespace monitoring port-forward $(kubectl get pods --namespace monitoring -l "app=grafana" -o jsonpath="{.items[0].metadata.name}") 3000
  ```
  The password for the `admin` account can be obtained with:
  ```shell
  $ kubectl get secret --namespace monitoring grafana -o jsonpath="{.data.admin-password}" | base64 --decode ; echo
  ```
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

Alerts are defined in the `serverFiles` section of the [prometheus values file](./values.prometheus.yaml). An alert consists of the following elements:
* A name defined by the `alert` key.
* A [PromQL](https://prometheus.io/docs/prometheus/latest/querying/examples/) expression defining a trigger condition based on the values of prometheus metrics. This is configured with the `expr` key.
* A delay for which the trigger condition has to be true to fire the alert. This is configured with the `for` key.
* A `labels` key that allow to specify a severity for each alert.
* An `annotations` key under which can be defined:
  * An `identifier` key that describes which instance is affected.
  * A `summary` key that sums the alert up in a short message.
  * A `description` key that details the alert in a longer message.

To write new alerts, you can check out [this collection](https://awesome-prometheus-alerts.grep.to/rules) or [this one](https://gitlab.com/gitlab-com/runbooks/tree/master/rules) of alerting rules or directly check the [prometheus documentation](https://prometheus.io/docs/prometheus/latest/configuration/alerting_rules/).

When you are done editing alerting rules, [redeploy prometheus](#arrow_upper_right-Update-configuration) with the new configuration.

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
* A `grafana` pod that hosts the grafana dashboard.

## :arrow_upper_right: Update configuration

The prometheus configuration is stored in the `[values.prometheus.yaml](./values.prometheus.yaml)`file. To update prometheus configuration, edit this file accordingly. The complete list of available variables and associated values can be found in the [default values file](https://github.com/helm/charts/blob/master/stable/prometheus/values.yaml). When you are ready to deploy the new configuration, run:
```shell
$ helm upgrade prometheus stable/prometheus -f values.prometheus.yaml --namespace monitoring
```

The grafana configuration is stored in the `[values.grafana.yaml](./values.grafana.yaml)`file. To update grafana configuration, edit this file accordingly. The complete list of available variables and associated values can be found in the [default values file](https://github.com/helm/charts/blob/master/stable/grafana/values.yaml). When you are ready to deploy the new configuration, run:
```shell
$ helm upgrade grafana stable/grafana -f values.grafana.yaml --namespace monitoring
```

## :construction: Installation

Prometheus is installed with the official [prometheus helm chart](https://github.com/helm/charts/tree/master/stable/prometheus) with a custom [values file](./values.prometheus.yaml) that overrides some defaults variables. Don't hesitate to customize and adapt the values file to your specific use case before installing. The complete list of available variables and associated values can be found in the [default values file](https://github.com/helm/charts/blob/master/stable/prometheus/values.yaml).

To install prometheus, make sure you have helm installed and configured to work with your cluster then run the following from the directory where this README is:
```shell
$ helm install stable/prometheus --name prometheus -f values.prometheus.yaml --namespace monitoring
```

Grafana is installed with the official [grafana helm chart](https://github.com/helm/charts/tree/master/stable/grafana) with a custom [values file](./values.grafana.yaml) that overrides some defaults variables. Don't hesitate to customize and adapt the values file to your specific use case before installing. The complete list of available variables and associated values can be found in the [default values file](https://github.com/helm/charts/blob/master/stable/grafana/values.yaml).

To install grafana, make sure you have helm installed and configured to work with your cluster then run the following from the directory where this README is:
```shell
$ helm install stable/grafana --name grafana -f values.grafana.yaml --namespace monitoring
```
