FROM rabbitmq:3-management
ADD rabbitmq_delayed_message_exchange-3.12.0.ez /plugins
RUN rabbitmq-plugins enable --offline rabbitmq_delayed_message_exchange
