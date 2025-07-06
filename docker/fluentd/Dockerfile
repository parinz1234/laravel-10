FROM fluent/fluentd:v1.16-debian-1

# Switch to root to install plugins
USER root

# Install the OpenSearch plugin
RUN gem install fluent-plugin-opensearch

# Switch back to fluent user
USER fluent 