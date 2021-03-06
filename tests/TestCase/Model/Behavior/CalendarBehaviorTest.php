<?php
namespace Calendar\Test\Model\Behavior;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CalendarBehaviorTest extends TestCase
{

    /**
     * @var array
     */
    public $fixtures = [
        'plugin.Calendar.Events'
    ];

    /**
     * @var \Cake\ORM\Table;
     */
    public $Events;

    /**
     * @var array
     */
    public $config = [
        'field' => 'beginning'
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Events = TableRegistry::get('Calendar.Events');
        $this->Events->addBehavior('Calendar.Calendar', $this->config);

        $this->db = ConnectionManager::get('test');

        $this->_addFixtureData();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Events);
        TableRegistry::clear();
    }

    /**
     * @return void
     */
    public function testFind()
    {
        $options = [
            'month' => 12,
            'year' => (int)date('Y'),
        ];

        $events = $this->Events->find('calendar', $options);

        $eventList = array_values($events->find('list')->toArray());
        $expected = [
            'One',
            '4 days',
            'Over new years eve',
        ];
        $this->assertEquals($expected, $eventList);
    }

    /**
     * Gets a new Entity
     *
     * @param array $data
     * @return \Cake\ORM\Entity
     */
    protected function _getEntity($data)
    {
        return new Entity($data);
    }

    /**
     * @return void
     */
    protected function _addFixtureData()
    {
        $data = [
            [
                'title' => 'Wrong',
                'beginning' => date('Y') . '-11-30',
            ],
            [
                'title' => 'One',
                'beginning' => date('Y') . '-12-28',
            ],
            [
                'title' => '4 days',
                'beginning' => date('Y') . '-12-14',
                'end' => date('Y') . '-12-18',
            ],
            [
                'title' => 'Over new years eve',
                'beginning' => date('Y') . '-12-29',
                'end' => (date('Y') + 1) . '-01-02',
            ],
        ];

        foreach ($data as $row) {
            $entity = $this->_getEntity($row);
            if (!$this->Events->save($entity)) {
                throw new InternalErrorException(print_r($entity->errors()));
            }
        }
    }

}

class TestController extends Controller
{

    /**
     * @var string
     */
    public $modelClass = 'Calendar.Events';

}
