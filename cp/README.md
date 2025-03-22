sudo microk8s.kubectl create namespace my-cp
namespace/my-cp created



helm dependency update my-helm-chart/
Getting updates for unmanaged Helm repositories...
...Successfully got an update from the "https://charts.bitnami.com/bitnami" chart repository
Saving 1 charts
Downloading rabbitmq from repo https://charts.bitnami.com/bitnami
Pulled: registry-1.docker.io/bitnamicharts/rabbitmq:15.4.0
Digest: sha256:5e3a3c31e2d9fc8265c49fcd4aceabda73fec50b27550bc327ea5cfe02

sudo microk8s.kubectl get secret --namespace my-cp-namespace my-cp-rabbitmq -o jsonpath="{.data.rabbitmq-password}" | base64 -d