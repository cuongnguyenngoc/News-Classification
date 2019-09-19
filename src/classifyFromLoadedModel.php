<?php

namespace NewsClassification;
set_time_limit(0);
ini_set('memory_limit', -1);

require __DIR__ . '/vendor/autoload.php';

use Phpml\ModelManager;
use Phpml\Dataset\FilesDataset;
use Phpml\CrossValidation\StratifiedRandomSplit;

$modelManager = new ModelManager();
$model = $modelManager->restoreFromFile('newsclassification.phpml');

$text = '';
print_r($model->predict([$text])[0]);


// $dataset = new FilesDataset('datasets/raw/bbc/bbc');

// $split = new StratifiedRandomSplit($dataset, 0.2);

// $testSamples = $split->getTestSamples();
// $testLabels = $split->getTestLabels();


// $predictedLabels = $model->predict($testSamples);

// print_r($predictedLabels);

// print_r($testLabels);

// use Phpml\Metric\Accuracy;
// echo 'Accuracy: '.Accuracy::score($testLabels, $predictedLabels);