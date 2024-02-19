<?php

namespace DVC\ContainerWrapper\Builder;

class TemplateBuilder
{
    public function __construct()
    {
    }

    public function templateForContainer(string $containerName): string
    {
        return
            '{% set containerClass = containerName %}
            <div class="{{ containerClass }}">{% block container %}{% endblock container %}</div>'
        ;
    }
}
