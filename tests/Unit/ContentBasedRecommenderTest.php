<?php

use App\Services\ContentBasedRecommender;

beforeEach(function () {
    $this->recommender = new ContentBasedRecommender();
});

test('jaccard index is 0 when both sets are empty', function () {
    expect($this->recommender->jaccardIndex([], []))->toBe(0);
});

test('jaccard index is 0 when sets share nothing', function () {
    // PHP's "/" returns an int (not a float) when the division is exact
    expect($this->recommender->jaccardIndex([1, 2], [3, 4]))->toBe(0);
});

test('jaccard index is 1 when sets are identical', function () {
    expect($this->recommender->jaccardIndex([1, 2, 3], [1, 2, 3]))->toBe(1);
});

test('jaccard index scales with partial overlap', function () {
    // intersection {2,3} = 2, union {1,2,3,4} = 4 -> 0.5
    expect($this->recommender->jaccardIndex([1, 2, 3], [2, 3, 4]))->toBe(0.5);
});

/**
 * Known bug: jaccardIndex() only array_unique()'s the union, not the two
 * input sets, and array_intersect() preserves duplicates from $set1. So a
 * duplicated id in $set1 is counted multiple times in the intersection,
 * inflating the score above the true Jaccard index. Here the "true" index
 * for {1,2} vs {1,2,3} would be 2/3, but the duplicated 1 in $set1 pushes
 * the intersection count to 3 (matching the union count), so it returns 1.
 */
test('jaccard index is inflated by duplicate ids in the first set', function () {
    expect($this->recommender->jaccardIndex([1, 1, 2], [1, 2, 3]))->toBe(1);
});
