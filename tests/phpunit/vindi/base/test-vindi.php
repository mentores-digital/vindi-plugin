
<?php

include_once VINDI_PATH . 'src/services/VindiHelpers.php';

class Vindi_Test extends Vindi_Test_Base
{
  public function test_constants_defined()
  {
    $this->assertTrue(defined('VINDI_VERSION'));
    $this->assertTrue(defined('VINDI'));
    $this->assertTrue(defined('VINDI_MININUM_WP_VERSION'));
    $this->assertTrue(defined('VINDI_MININUM_PHP_VERSION'));
    $this->assertTrue(defined('VINDI_FILE'));
    $this->assertTrue(defined('VINDI_PLUGIN_BASE'));
    $this->assertTrue(defined('VINDI_PATH'));
  }
  /**
   * Vindi requires statement_descriptor to be no longer than 22 characters.
   * In addition, it cannot contain <>"' special characters.
   */
  public function test_statement_descriptor_sanitation()
  {
    $statement_descriptor1 = array(
      'actual'   => 'Test\'s Store',
      'expected' => 'Tests Store',
    );

    $this->assertEquals($statement_descriptor1['expected'], VindiHelpers::clean_statement_descriptor($statement_descriptor1['actual']));

    $statement_descriptor2 = array(
      'actual'   => 'Test\'s Store > Driving Course Range',
      'expected' => 'Tests Store  Driving C',
    );

    $this->assertEquals($statement_descriptor2['expected'], VindiHelpers::clean_statement_descriptor($statement_descriptor2['actual']));

    $statement_descriptor3 = array(
      'actual'   => 'Test\'s Store < Driving Course Range',
      'expected' => 'Tests Store  Driving C',
    );

    $this->assertEquals($statement_descriptor3['expected'], VindiHelpers::clean_statement_descriptor($statement_descriptor3['actual']));

    $statement_descriptor4 = array(
      'actual'   => 'Test\'s Store " Driving Course Range',
      'expected' => 'Tests Store  Driving C',
    );

    $this->assertEquals($statement_descriptor4['expected'], VindiHelpers::clean_statement_descriptor($statement_descriptor4['actual']));
  }

  /**
   * Vindi requires price in the smallest dominations aka cents.
   * This test will see if we're indeed converting the price correctly.
   */
  public function test_price_conversion_before_send_to_vindi()
  {
    $this->assertEquals(10050, VindiHelpers::get_vindi_amount(100.50, 'BRL'));
    $this->assertInternalType('int', VindiHelpers::get_vindi_amount(100.50, 'BRL'));
  }
}; ?>
