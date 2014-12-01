<?php

use SQRT\Controller;

class defaultController extends Controller
{
  function indexAction()
  {
    return 'index';
  }

  function testAction()
  {
    return 'test';
  }

  function someAction()
  {
    // Не будет вызван, т.к. перебивается someController
    return 'never';
  }
}