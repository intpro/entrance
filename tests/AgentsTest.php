<?php

namespace Interpro\Entrance\Test;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase;
use Interpro\Extractor\Contracts\Items\BlockItem;

//php vendor/bin/phpunit --verbose ./packages/interpro/Entrance
class AgentsTest extends TestCase
{
    use DatabaseMigrations;

    private $initAgent;
    private $syncAgent;
    private $updateAgent;
    private $destructAgent;
    private $extractAgent;

    /**
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        $this->syncAgent     = $this->app->make('Interpro\Entrance\Contracts\CommandAgent\SyncAgent');
        $this->initAgent     = $this->app->make('Interpro\Entrance\Contracts\CommandAgent\InitAgent');
        $this->updateAgent   = $this->app->make('Interpro\Entrance\Contracts\CommandAgent\UpdateAgent');
        $this->destructAgent = $this->app->make('Interpro\Entrance\Contracts\CommandAgent\DestructAgent');
        $this->extractAgent  = $this->app->make('Interpro\Entrance\Contracts\Extract\ExtractAgent');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    private function hashBlock(BlockItem $block)
    {
        $we_have = 'owns:';

        $owns = $block->getOwns();

        foreach($owns as $own)
        {
            $we_have .= '('.$own->getName().', '.$own->getFieldMeta()->getFieldType()->getName().', '.$own->getItem()->getValue().')';
        }

        $we_have .= '|flat:';

        $groups = $block->getGroupSetFlat();

        foreach($groups as $group)
        {
            $we_have .= '('.$group->getName().')';
        }

        $we_have .= '|groups:';

        $groups = $block->getGroupSet();

        foreach($groups as $group)
        {
            $we_have .= '('.$group->getName().')';
        }

        return $we_have;
    }

    public function testLife()
    {
        $this->sync();
        $this->init();
        $this->update();
        $this->destruct();
    }
    
    public function sync()
    {
        //Создаст блоки
        $this->syncAgent->syncAll();

        //Тест feed feedback

        $FeedbackBlock = $this->extractAgent->getBlock('feedback');
        $form1Block = $this->extractAgent->getBlock('form1');
        $form2Block = $this->extractAgent->getBlock('form2'); //формы хранятся там же где feedback блок, получаются так же

        $must_be = 'owns:(name, string, feedback)(from, string, )(to, string, )(subject, string, )(username, string, )(password, string, )(host, string, )(port, string, )(encryption, string, )(descr1, string, )(descr2, string, )(number1, int, 0)(number2, int, 0)|flat:(mailfromac)|groups:(mailfromac)';
        $we_have = $this->hashBlock($FeedbackBlock);

        $this->assertEquals(
            $we_have, $must_be
        );


        //Тест QS

        $birds_block = $this->extractAgent->getBlock('block_birds');

        $must_be = 'owns:(name, string, block_birds)(title, string, Блок block_birds)(show, bool, 1)(descr, string, )(seotitle, seo, )(seodescription, seo, )(seokeywords, seo, )|flat:(group_bird_type)(group_bird_class)(group_bird_area)|groups:(group_bird_type)';
        $we_have = $this->hashBlock($birds_block);

        $this->assertEquals(
            $we_have, $must_be
        );

        //-------------------------------------------------

        $bird_areas = $this->extractAgent->getBlock('block_areas');

        $must_be = 'owns:(name, string, block_areas)(title, string, Блок block_areas)(show, bool, 1)(descr, string, )|flat:(group_area)|groups:(group_area)';
        $we_have = $this->hashBlock($bird_areas);

        $this->assertEquals(
            $we_have, $must_be
        );
    }

    public function init()
    {
        //Тест feed feedback
        $mail = $this->initAgent->init('form1_mail', ['descr1' => 'Извещение об уплате', 'subject' => 'Сабджект']);

        $this->assertEquals(
            'Извещение об уплате', $mail->descr1
        );

        $this->assertEquals(
            'Сабджект', $mail->subject
        );


        //Тест QS
        //Набьем элементами группы и проверим соответствие выборки ожиданиям
        $pinguinItem = $this->initAgent->init('group_bird_type', ['slug' => 'pinguin', 'descr' => 'Все представители этого семейства хорошо плавают и ныряют.', 'title' => 'Пингвиновые']);
        $chickenItem = $this->initAgent->init('group_bird_type', ['slug' => 'chicken', 'descr' => 'У них крепкие лапы, приспособленные для быстрого бега и рытья земли.', 'title' => 'Курообразные']);
        $duckItem = $this->initAgent->init('group_bird_type',    ['slug' => 'duck', 'descr' => 'Гуси, утки, лебеди.', 'title' => 'Гусеобразные']);

        //Области обитания
        $areaTropicItem = $this->initAgent->init('group_area', ['slug' => 'tropic', 'descr' => 'Влажные леса на разных континентах.', 'title' => 'Тропики']);
        $areaNordItem = $this->initAgent->init('group_area', ['slug' => 'nord', 'descr' => 'Северные широты евразии и америки.', 'title' => 'Север']);
        $areaAntarcticItem = $this->initAgent->init('group_area', ['slug' => 'antarctic', 'descr' => 'Антарктида и острова вокруг.', 'title' => 'Антарктика']);

        //Виды птиц
        $pinguinImperorItem = $this->initAgent->init('group_bird_class', ['superior' => $pinguinItem->id, 'slug' => 'imperor_pinguin', 'descr' => 'Самый крупный и тяжёлый из современных видов семейства.', 'title' => 'Императорский']);
        $pinguinAdeliItem   = $this->initAgent->init('group_bird_class', ['superior' => $pinguinItem->id, 'slug' => 'adeli_pinguin', 'descr' => 'Один из самых распространенных видов.', 'title' => 'Адели']);

        $chickenTeterevItem = $this->initAgent->init('group_bird_class', ['superior' => $chickenItem->id, 'slug' => 'teterev_chicken', 'descr' => 'Оседлая либо кочующая птица.', 'title' => 'Тетерев']);
        $chickenFazanItem   = $this->initAgent->init('group_bird_class', ['superior' => $chickenItem->id, 'slug' => 'fazan_chicken', 'descr' => 'Все признаки для фазановых в равной мере характерны, как и для курообразных вообще.', 'title' => 'Фазан']);

        $duckGusItem = $this->initAgent->init('group_bird_class', ['superior' => $duckItem->id, 'slug' => 'gus_duck', 'descr' => 'Гуси отличаются клювом, имеющим при основании большую высоту, чем ширину, и оканчивающимся ноготком с острым краем.', 'title' => 'Гусь']);
        $duckDuckItem = $this->initAgent->init('group_bird_class', ['superior' => $duckItem->id, 'slug' => 'duck_duck', 'descr' => 'Птицы средних и небольших размеров с относительно короткой шеей.', 'title' => 'Утка']);


        $must_be = '';

        $must_be .= 'pinguin:(descr:Все представители этого семейства хорошо плавают и ныряют.,title:Пингвиновые),';
        $must_be .= 'chicken:(descr:У них крепкие лапы, приспособленные для быстрого бега и рытья земли.,title:Курообразные),';
        $must_be .= 'duck:(descr:Гуси, утки, лебеди.,title:Гусеобразные),';

        $must_be .= 'tropic:(descr:Влажные леса на разных континентах.,title:Тропики),';
        $must_be .= 'nord:(descr:Северные широты евразии и америки.,title:Север),';
        $must_be .= 'antarctic:(descr:Антарктида и острова вокруг.,title:Антарктика),';

        $must_be .= 'imperor_pinguin:(descr:Самый крупный и тяжёлый из современных видов семейства.,title:Императорский,superior:'.$pinguinItem->id.'),';
        $must_be .= 'adeli_pinguin:(descr:Один из самых распространенных видов.,title:Адели,superior:'.$pinguinItem->id.'),';

        $must_be .= 'teterev_chicken:(descr:Оседлая либо кочующая птица.,title:Тетерев,superior:'.$chickenItem->id.'),';
        $must_be .= 'fazan_chicken:(descr:Все признаки для фазановых в равной мере характерны, как и для курообразных вообще.,title:Фазан,superior:'.$chickenItem->id.'),';

        $must_be .= 'gus_duck:(descr:Гуси отличаются клювом, имеющим при основании большую высоту, чем ширину, и оканчивающимся ноготком с острым краем.,title:Гусь,superior:'.$duckItem->id.'),';
        $must_be .= 'duck_duck:(descr:Птицы средних и небольших размеров с относительно короткой шеей.,title:Утка,superior:'.$duckItem->id.'),';


        $we_have = '';

        $we_have .= $pinguinItem->slug.':(descr:'.$pinguinItem->descr.',title:'.$pinguinItem->title.'),';
        $we_have .= $chickenItem->slug.':(descr:'.$chickenItem->descr.',title:'.$chickenItem->title.'),';
        $we_have .= $duckItem->slug.':(descr:'.$duckItem->descr.',title:'.$duckItem->title.'),';

        $we_have .= $areaTropicItem->slug.':(descr:'.$areaTropicItem->descr.',title:'.$areaTropicItem->title.'),';
        $we_have .= $areaNordItem->slug.':(descr:'.$areaNordItem->descr.',title:'.$areaNordItem->title.'),';
        $we_have .= $areaAntarcticItem->slug.':(descr:'.$areaAntarcticItem->descr.',title:'.$areaAntarcticItem->title.'),';

        $we_have .= $pinguinImperorItem->slug.':(descr:'.$pinguinImperorItem->descr.',title:'.$pinguinImperorItem->title.',superior:'.$pinguinItem->id.'),';
        $we_have .= $pinguinAdeliItem->slug.':(descr:'.$pinguinAdeliItem->descr.',title:'.$pinguinAdeliItem->title.',superior:'.$pinguinItem->id.'),';

        $we_have .= $chickenTeterevItem->slug.':(descr:'.$chickenTeterevItem->descr.',title:'.$chickenTeterevItem->title.',superior:'.$chickenItem->id.'),';
        $we_have .= $chickenFazanItem->slug.':(descr:'.$chickenFazanItem->descr.',title:'.$chickenFazanItem->title.',superior:'.$chickenItem->id.'),';

        $we_have .= $duckGusItem->slug.':(descr:'.$duckGusItem->descr.',title:'.$duckGusItem->title.',superior:'.$duckItem->id.'),';
        $we_have .= $duckDuckItem->slug.':(descr:'.$duckDuckItem->descr.',title:'.$duckDuckItem->title.',superior:'.$duckItem->id.'),';



        $this->assertEquals(
            $we_have, $must_be
        );

        $birdsClasses = $this->extractAgent->countGroup('group_bird_class');
        $birdsTypes = $this->extractAgent->countGroup('group_bird_type');
        $areas = $this->extractAgent->countGroup('group_area');

        $this->assertEquals(6, $birdsClasses);
        $this->assertEquals(3, $birdsTypes);
        $this->assertEquals(3, $areas);

        //Пронумеровался ли сортировщик?
        $this->assertEquals(1, $pinguinImperorItem->sorter);
        $this->assertEquals(2, $pinguinAdeliItem->sorter);

        $this->assertEquals(1, $chickenTeterevItem->sorter);
        $this->assertEquals(2, $chickenFazanItem->sorter);

        $this->assertEquals(1, $duckGusItem->sorter);
        $this->assertEquals(2, $duckDuckItem->sorter);


        $this->extractAgent->reset();
        $selection = $this->extractAgent->selectGroup('group_bird_type');

        $maxid = $selection->get()->maxByField('sorter');
        $minid = $selection->get()->minByField('sorter');
        $sumid = $selection->get()->sumByField('sorter');

        $this->assertEquals(3, $maxid->sorter);
        $this->assertEquals(1, $minid->sorter);
        $this->assertEquals(6, $sumid);
    }

    public function update()
    {
        //Тест feed feedback
        $this->updateAgent->update('form1', 0, ['descr1' => 'Извещение об уплате', 'subject' => 'Сабджект']);
        $this->extractAgent->reset();
        $form1 = $this->extractAgent->getBlock('form1');

        $this->assertEquals(
            'Извещение об уплате', $form1->descr1
        );

        $this->assertEquals(
            'Сабджект', $form1->subject
        );

        //Тест QS

        $this->updateAgent->update('block_birds', 0, ['descr' => 'Птицы', 'seodescription' => 'Птицы seo', 'show' => false]);
        $this->updateAgent->update('block_areas', 0, ['descr' => 'Области обитания']);


        $selection = $this->extractAgent->selectGroup('group_bird_type', 'pinguin');
        $selection->eq('slug', 'pinguin');
        $pinguin = $selection->get()->first();

        $selection = $this->extractAgent->selectGroup('group_bird_type', 'chicken');
        $selection->eq('slug', 'chicken');
        $chicken = $selection->get()->first();

        $selection = $this->extractAgent->selectGroup('group_bird_type', 'duck');
        $selection->eq('slug', 'duck');
        $duck = $selection->get()->first();


        $impPnguin = $this->extractAgent->getBySlug('group_bird_class', 'imperor_pinguin');
        $tetChick = $this->extractAgent->getBySlug('group_bird_class', 'teterev_chicken');
        $gusDuck = $this->extractAgent->getBySlug('group_bird_class', 'gus_duck');


        //Установка ссылок на примеры птиц

        $this->updateAgent->update('group_bird_type', $pinguin->id, ['example' => $impPnguin->id]);

        $this->updateAgent->update('group_bird_type', $chicken->id, ['example' => $tetChick->id]);

        $this->updateAgent->update('group_bird_type', $duck->id, ['example' => $gusDuck->id, 'seotitle' => 'Тайтл утиных seo']);


        $this->extractAgent->reset();
        //Заново получаем и проверяем, что получилось

        $birds = $this->extractAgent->getBlock('block_birds');
        $areas = $this->extractAgent->getBlock('block_areas');

        $this->assertEquals(
            false, $birds->show
        );

        $this->assertEquals(
            'Птицы', $birds->descr
        );

        $this->assertEquals(
            'Птицы seo', $birds->seodescription
        );

        $this->assertEquals(
            'Области обитания', $areas->descr
        );

        //---------------------------------------------------

        $selection = $this->extractAgent->selectGroup('group_bird_type', 'pinguin');
        $selection->eq('slug', 'pinguin');
        $pinguin = $selection->get()->first();

        $selection = $this->extractAgent->selectGroup('group_bird_type', 'chicken');
        $selection->eq('slug', 'chicken');
        $chicken = $selection->get()->first();

        $selection = $this->extractAgent->selectGroup('group_bird_type', 'duck');
        $selection->eq('slug', 'duck');
        $duck = $selection->get()->first();


        $this->assertEquals(
            $pinguin->example, $impPnguin->id
        );

        $this->assertEquals(
            $chicken->example, $tetChick->id
        );

        $this->assertEquals(
            $duck->example, $gusDuck->id
        );

        $this->assertEquals(
            'Тайтл утиных seo', $duck->seotitle
        );

    }

    public function destruct()
    {
        $blocks = $this->extractAgent->getBlocks();

        foreach($blocks as $block)
        {
            //По иерархии superior спускаемся и удаляем от вложенных к внешним все элементы:

            $group0Set = $block->getGroupSet();

            foreach($group0Set as $group0level)
            {
                foreach($group0level as $group0Item)
                {
                    $group1Set = $group0Item->getGroupSet();
                    foreach($group1Set as $group1level)
                    {
                        foreach($group1level as $group1Item)
                        {
                            $ref1 = $group1Item->getSelfRef();
                            $this->destructAgent->delete($ref1->getType()->getName(), $ref1->getId());
                        }
                    }

                    $ref0 = $group0Item->getSelfRef();
                    $this->destructAgent->delete($ref0->getType()->getName(), $ref0->getId());
                }
            }

            //Здесь можно удалить и блоки...
        }

        $feedbackAc = $this->extractAgent->countGroup('mailfromac');
        $form1Mail = $this->extractAgent->countGroup('form1_mail');
        $form2Mail = $this->extractAgent->countGroup('form2_mail');
        $form1MailTo = $this->extractAgent->countGroup('form1_mailto');
        $form2MailTo = $this->extractAgent->countGroup('form2_mailto');

        $this->assertEquals(0, $feedbackAc);
        $this->assertEquals(0, $form1Mail);
        $this->assertEquals(0, $form2Mail);
        $this->assertEquals(0, $form1MailTo);
        $this->assertEquals(0, $form2MailTo);


        $birdsClasses = $this->extractAgent->countGroup('group_bird_class');
        $birdsTypes = $this->extractAgent->countGroup('group_bird_type');
        $areas = $this->extractAgent->countGroup('group_area');

        $this->assertEquals(0, $birdsClasses);
        $this->assertEquals(0, $birdsTypes);
        $this->assertEquals(0, $areas);
    }

}
