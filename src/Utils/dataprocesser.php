<?php

namespace NewsClassification\Utils;


use Phpml\Dataset\FilesDataset;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\NGramTokenizer;
use Phpml\FeatureExtraction\StopWords\English;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Dataset\ArrayDataset;
use Phpml\Classification\SVC;
use Phpml\Exception\FileException;


class DataProcessor {

    private $dataset;

    private $trainingSamples;
    private $trainingLabels;

    private $testSamples;
    private $testLabels;

    public function getDataset($datapath) {
        print_r("it should be something ", $datapath);
        $this->dataset = new FilesDataset($datapath);
        return $this->dataset;
    }

    public function transformData($samples) {
        $vectorizer = new TokenCountVectorizer(new NGramTokenizer(1,3), new English());
        $vectorizer->fit($samples);
        $vectorizer->transform($samples);

        $tfidftransfromer = new TfIdfTransformer();
        $tfidftransfromer->fit($samples);
        $tfidftransfromer->transform($samples);
    }

    public function seperateTrainingAndTesting($dataset) {
        $split = new StratifiedRandomSplit($dataset, 0.2);
        $this->trainingSamples = $split->getTrainSamples();
        $this->trainingLabels = $split->getTrainLabels();

        $this->testSamples = $split->getTestSamples();
        $this->testLabels = $split->getTestLabels();
    }

    public function getTrainingSamples() {
        return $this->trainingSamples;
    }

    public function getTestSamples() {
        return $this->testSamples;
    }

    public function getTrainingLabels() {
        return $this->trainingLabels;
    }

    public function getTestLabels() {
        return $this->testLabels;
    }

    function checkFilExists($filepath, $mode = 'rb')
    {
        if (!file_exists($filepath)) {
            return false;
        }
        return true;
    }
}