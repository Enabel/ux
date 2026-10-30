<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Table;

use Pagerfanta\PagerfantaInterface;

/**
 * A paginated list in a card: optional filters, the table, the range and the
 * pagination. The calling template renders the header cells (`head` block,
 * with Enabel:Ux:TableHeader) and the cells of each `item` (`row` block).
 */
class Table
{
    /**
     * @var PagerfantaInterface<mixed>
     */
    public PagerfantaInterface $pager;

    public string $emptyMessage = '';
}
