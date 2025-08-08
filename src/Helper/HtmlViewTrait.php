<?php


namespace Tiagolopes\SwoolePhp\Helper;

trait HtmlViewTrait
{
    private function getHtmlFromTemplate(string $template, array $data = []): string
    {
        ob_start();
        extract($data);
        require __DIR__ . '/../View/' . $template;

        return ob_get_clean();
    }
}
