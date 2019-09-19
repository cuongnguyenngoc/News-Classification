<?php

namespace NewsClassification;
require __DIR__ . '/vendor/autoload.php';

set_time_limit(0);
ini_set('memory_limit', -1);

use Phpml\Dataset\FilesDataset;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\NGramTokenizer;
use Phpml\FeatureExtraction\StopWords\English;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Dataset\ArrayDataset;
use Phpml\Classification\SVC;
use Phpml\Pipeline;

$dataset = new FilesDataset('datasets/raw/bbc');
$samples = $dataset->getSamples();

$split = new StratifiedRandomSplit($dataset, 0.2);
$trainingSamples = $split->getTrainSamples();
$trainingLabels = $split->getTrainLabels();

$testSamples = $split->getTestSamples();
$testLabels = $split->getTestLabels();

$pipeline = new Pipeline([
    new TokenCountVectorizer(new NGramTokenizer(1, 3), new English()),
    new TfIdfTransformer()
], new SVC());
$pipeline->train($trainingSamples, $trainingLabels);

$predictedLabels = $pipeline->predict($testSamples);

use Phpml\Metric\Accuracy;
echo 'Accuracy: '.Accuracy::score($testLabels, $predictedLabels);

#save model
use Phpml\ModelManager;

$modelManager = new ModelManager();
$modelManager->saveToFile($pipeline, 'newsclassification.phpml');