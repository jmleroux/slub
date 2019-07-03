<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\UI\Http;

use Slub\Domain\Entity\PR\PR;
use Slub\Domain\Repository\PRRepositoryInterface;
use Tests\WebTestCase;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 */
class ListPRsActionTest extends WebTestCase
{
    /** @var PRRepositoryInterface */
    private $PRRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->PRRepository = $this->get('slub.infrastructure.persistence.pr_repository');
        $this->PRRepository->reset();
    }

    /**
     * @test
     */
    public function it_lists_all_the_prs_and_calculates_the_time_to_merge(): void
    {
        $this->PRRepository->save(
            PR::fromNormalized(
                [
                    'IDENTIFIER'       => 'akeneo/pim-community-dev/1111',
                    'GTMS'             => 1,
                    'NOT_GTMS'         => 1,
                    'COMMENTS'         => 1,
                    'CI_STATUS'        => 'PENDING',
                    'IS_MERGED'        => true,
                    'CHANNEL_IDS'      => ['squad-raccoons'],
                    'MESSAGE_IDS'      => ['1', '2'],
                    'PUT_TO_REVIEW_AT' => '1560177798',
                    'MERGED_AT'        => '1561363426',
                ]
            )
        );
        $this->PRRepository->save(
            PR::fromNormalized(
                [
                    'IDENTIFIER'       => 'akeneo/pim-community-dev/2222',
                    'GTMS'             => 1,
                    'NOT_GTMS'         => 1,
                    'COMMENTS'         => 1,
                    'CI_STATUS'        => 'PENDING',
                    'IS_MERGED'        => false,
                    'CHANNEL_IDS'      => ['squad-raccoons'],
                    'MESSAGE_IDS'      => ['1', '2'],
                    'PUT_TO_REVIEW_AT' => '1560177798',
                    'MERGED_AT'        => null,
                ]
            )
        );

        $client = static::createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [
                'AVERAGE_TIME_TO_MERGE' => 13,
                [
                    'IDENTIFIER'       => 'akeneo/pim-community-dev/2222',
                    'GTMS'             => 1,
                    'NOT_GTMS'         => 1,
                    'COMMENTS'         => 1,
                    'CI_STATUS'        => 'PENDING',
                    'IS_MERGED'        => false,
                    'MESSAGE_IDS'      => ['1', '2'],
                    'CHANNEL_IDS'      => ['squad-raccoons'],
                    'PUT_TO_REVIEW_AT' => '1560177798',
                    'MERGED_AT'        => null,
                ],
                [
                    'IDENTIFIER'       => 'akeneo/pim-community-dev/1111',
                    'GTMS'             => 1,
                    'NOT_GTMS'         => 1,
                    'COMMENTS'         => 1,
                    'CI_STATUS'        => 'PENDING',
                    'IS_MERGED'        => true,
                    'MESSAGE_IDS'      => ['1', '2'],
                    'CHANNEL_IDS'      => ['squad-raccoons'],
                    'PUT_TO_REVIEW_AT' => '1560177798',
                    'MERGED_AT'        => '1561363426',
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }
}
