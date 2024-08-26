<?php $errorId = uniqid('error', true); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Monarch Error</title>
    <style><?= include __DIR__ .'/errors.css' ?></style>
    <script><?= include __DIR__ .'/errors.js' ?></script>
</head>
<body>
    <header>
        <div class="container">
            <h1><?= $title ?></h1>
            <p class="lead"><?= $message ?></p>
        </div>
    </header>

    <?php if (! empty($solution)): ?>
        <section class="solution">
            <div class="container">
                <p><?= escapeHtml($solution) ?></p>
            </div>
        </section>
    <?php endif ?>

    <div class="container error-location">
        <!-- Source -->
        <div class="container">
            <p><b><?= $this->cleanPath($file) ?></b> at line <b><?= $line ?></b></p>

            <?php if (is_file($file)) : ?>
                <div class="source">
                    <?= $this->highlightFile($file, $line, 15); ?>
                </div>
            <?php endif; ?>
        </div>

        <p id="tabs" class="tabs">
            <a data-tab="header-wrap" class="active">Headers</a>
            <a data-tab="trace-wrap">Back Trace</a>
        </p>

        <div id="tab-content">
            <div id="trace-wrap" style="display: none">
                <ol class="trace">
                    <?php foreach ($trace as $index => $row) : ?>
                        <li>
                            <p>
                                <!-- Trace info -->
                                <?php if (isset($row['file']) && is_file($row['file'])) : ?>
                                    <?php
                                    if (isset($row['function']) && in_array($row['function'], ['include', 'include_once', 'require', 'require_once'], true)) {
                                        echo escapeHtml($row['function'] . ' ' . $this->cleanPath($row['file']));
                                    } else {
                                        echo escapeHtml($this->cleanPath($row['file']) . ' : ' . $row['line']);
                                    }
                                    ?>
                                <?php else: ?>
                                    {PHP internal code}
                                <?php endif; ?>

                                <!-- Class/Method -->
                                <?php if (isset($row['class'])) : ?>
                                    &nbsp;&nbsp;&mdash;&nbsp;&nbsp;<?= escapeHtml($row['class'] . $row['type'] . $row['function']) ?>
                                    <?php if (! empty($row['args'])) : ?>
                                        <?php $argsId = $errorId . 'args' . $index ?>
                                        ( <a href="#" onclick="return toggle('<?= escapeHtmlAttr($argsId) ?>');">arguments</a> )
                                        <div class="args" id="<?= escapeHtmlAttr($argsId) ?>">
                                            <table cellspacing="0">

                                            <?php
                                            $params = null;
                                            // Reflection by name is not available for closure function
                                            if (substr($row['function'], -1) !== '}') {
                                                $mirror = isset($row['class']) ? new ReflectionMethod($row['class'], $row['function']) : new ReflectionFunction($row['function']);
                                                $params = $mirror->getParameters();
                                            }

                                            foreach ($row['args'] as $key => $value) : ?>
                                                <tr>
                                                    <td><code><?= escapeHtml(isset($params[$key]) ? '$' . $params[$key]->name : "#{$key}") ?></code></td>
                                                    <td><pre><?= escapeHtml(print_r($value, true)) ?></pre></td>
                                                </tr>
                                            <?php endforeach ?>

                                            </table>
                                        </div>
                                    <?php else : ?>
                                        ()
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (! isset($row['class']) && isset($row['function'])) : ?>
                                    &nbsp;&nbsp;&mdash;&nbsp;&nbsp;    <?= escapeHtml($row['function']) ?>()
                                <?php endif; ?>
                            </p>

                            <!-- Source? -->
                            <?php if (isset($row['file']) && is_file($row['file']) && isset($row['class'])) : ?>
                                <div class="source">
                                    <?= $this->highlightFile($row['file'], $row['line']) ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>

            <!-- Headers Tab -->
            <div id="header-wrap">
                <table cellspacing="0">
                    <?php foreach ($headers as $name => $value) : ?>
                        <tr>
                            <td><?= escapeHtml($name) ?></td>
                            <td><?= escapeHtml($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
    </div>
</body>
