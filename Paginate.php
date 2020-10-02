<?php

/*
include "Paginate.php";

$data = json_decode(file_get_contents('data/list_links.txt'), true);
$total = count($data);

$config['current_page'] = isset($_GET['trang']) ? $_GET['trang'] : 1;
$config['total_rows'] = $total;
$config['base_url'] = 'Paginate_data.php?trang=(:num)';
$config['per_page'] = 12;
$config['num_links'] = 7;
$config['prev_link'] = '&laquo; Trước';
$config['next_link'] = 'Sau &raquo;';

$paginate = new Paginate();
$paginate->initialize($config);

$data = $paginate->get_array($data);

foreach ($data as $row) {
	echo '<p>' . $row . '</p>';
}

echo $paginate->create_links();
*/

class Paginate
{
	protected $_config = array(
		'current_page' => 1, // Trang hiện tại
		'total_rows' => 1, // Tổng số record
		'total_pages' => 1, // Tổng số trang
		'per_page' => 10, // limit
		'base_url' => '', // Link full có dạng như sau: domain/com/page/(:num)
		'num_links' => 9, // Số button trang bạn muốn hiển thị
		'next_link' => 'Next',
		'prev_link' => 'Previous'
	);

	/*
	 * Hàm khởi tạo ban đầu để sử dụng phân trang
	*/
	function initialize($config = array())
	{
		/*
		 * Lặp qua từng phần tử config truyền vào và gán vào config của đối tượng
		 * trước khi gán vào thì phải kiểm tra thông số config truyền vào có nằm
		 * trong hệ thống config không, nếu có thì mới gán
		*/
		foreach ($config as $key => $val)
		{
			if (isset($this->_config[$key]))
			{
				$this->_config[$key] = $val;
			}
		}

		/*
		 * Kiểm tra thông số limit truyền vào có nhỏ hơn 0 hay không?
		 * Nếu nhỏ hơn thì gán cho limit = 0, vì trong mysql không cho limit bé hơn 0
		*/
		if ($this->_config['per_page'] < 0)
		{
			$this->_config['per_page'] = 0;
		}

		/*
		 * Tính total page, công tức tính tổng số trang như sau:
		 * total_page = ciel(total_record/limit).
		 * Tại sao lại như vậy? Đây là công thức tính trung bình thôi, ví
		 * dụ tôi có 1000 record và tôi muốn mỗi trang là 100 record thì
		 * đương nhiên sẽ lấy 1000/100 = 10 trang đúng không nào :D
		*/
		$this->_config['total_pages'] = ceil($this->_config['total_rows'] / $this->_config['per_page']);

		/*
		 * Sau khi có tổng số trang ta kiểm tra xem nó có nhỏ hơn 0 hay không
		 * nếu nhỏ hơn 0 thì gán nó băng 1 ngay. Vì mặc định tổng số trang luôn bằng 1
		*/
		if (!$this->_config['total_pages'])
		{
			$this->_config['total_pages'] = 1;
		}

		/*
		 * Trang hiện tại sẽ rơi vào một trong các trường hợp sau:
		 *  - Nếu người dùng truyền vào số trang nhỏ hơn 1 thì ta sẽ gán nó = 1
		 *  - Nếu trang hiện tại người dùng truyền vào lớn hơn tổng số trang
		 *    thì ta gán nó bằng tổng số trang
		 * Đây là vấn đề giúp web chạy trơn tru hơn, vì đôi khi người dùng cố ý
		 * thay đổi tham số trên url nhằm kiểm tra lỗi web của chúng ta
		*/
		if ($this->_config['current_page'] < 1)
		{
			$this->_config['current_page'] = 1;
		}

		if ($this->_config['current_page'] > $this->_config['total_pages'])
		{
			$this->_config['current_page'] = $this->_config['total_pages'];
		}

	}

	/**
	 * @return int
	 */
	public function get_current_page()
	{
		return $this->_config['current_page'];
	}

	public function get_total_pages()
	{
		return $this->_config['total_pages'];
	}

	public function get_page_url($page_num)
	{
		return str_replace('(:num)', $page_num, $this->_config['base_url']);
	}

	public function get_next_page()
	{
		if ($this->_config['current_page'] < $this->_config['total_pages'])
		{
			return $this->_config['current_page'] + 1;
		}

		return null;
	}

	public function get_prev_page()
	{
		if ($this->_config['current_page'] > 1)
		{
			return $this->_config['current_page'] - 1;
		}

		return null;
	}

	public function get_next_url()
	{
		if (!$this->get_next_page())
		{
			return null;
		}

		return $this->get_page_url($this->get_next_page());
	}

	/**
	 * @return string|null
	 */
	public function get_prev_url()
	{
		if (!$this->get_prev_page())
		{
			return null;
		}

		return $this->get_page_url($this->get_prev_page());
	}

	/**
	 * @return int
	 */
	public function get_start_row()
	{
		return ($this->_config['current_page'] - 1) * $this->_config['per_page'];
	}

	/**
	 * return array
	 */
	public function get_array(array $array)
	{
		return array_slice( $array, $this->get_start_row(), $this->_config['per_page'] );
	}

	/**
	 * Get an array of paginated page data.
	 *
	 * Example:
	 * array(
	 *     array ('num' => 1,     'url' => '/example/page/1',  'isCurrent' => false),
	 *     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
	 *     array ('num' => 3,     'url' => '/example/page/3',  'isCurrent' => false),
	 *     array ('num' => 4,     'url' => '/example/page/4',  'isCurrent' => true ),
	 *     array ('num' => 5,     'url' => '/example/page/5',  'isCurrent' => false),
	 *     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
	 *     array ('num' => 10,    'url' => '/example/page/10', 'isCurrent' => false),
	 * )
	 *
	 * @return array
	 */
	public function get_pages()
	{
		$pages = array();

		if ($this->_config['total_pages'] <= 1)
		{
			return array();
		}

		if ($this->_config['total_pages'] <= $this->_config['num_links'])
		{
			for ($i = 1;$i <= $this->_config['total_pages'];$i++)
			{
				$pages[] = $this->create_page($i, $i == $this->_config['current_page']);
			}
		}
		else
		{

			// Determine the sliding range, centered around the current page.
			$num_adjacents = (int)floor(($this->_config['num_links'] - 3) / 2);

			if ($this->_config['current_page'] + $num_adjacents > $this->_config['total_pages'])
			{
				$sliding_start = $this->_config['total_pages'] - $this->_config['num_links'] + 2;
			}
			else
			{
				$sliding_start = $this->_config['current_page'] - $num_adjacents;
			}
			if ($sliding_start < 2) $sliding_start = 2;

			$sliding_end = $sliding_start + $this->_config['num_links'] - 3;
			if ($sliding_end >= $this->_config['total_pages']) $sliding_end = $this->_config['total_pages'] - 1;

			// Build the list of pages.
			$pages[] = $this->create_page(1, $this->_config['current_page'] == 1);
			if ($sliding_start > 2)
			{
				$pages[] = $this->create_page_ellipsis();
			}
			for ($i = $sliding_start;$i <= $sliding_end;$i++)
			{
				$pages[] = $this->create_page($i, $i == $this->_config['current_page']);
			}
			if ($sliding_end < $this->_config['total_pages'] - 1)
			{
				$pages[] = $this->create_page_ellipsis();
			}
			$pages[] = $this->create_page($this->_config['total_pages'], $this->_config['current_page'] == $this->_config['total_pages']);
		}

		return $pages;
	}

	/**
	 * Create a page data structure.
	 *
	 * @param int $page_num
	 * @param bool $is_current
	 * @return Array
	 */
	protected function create_page($page_num, $is_current = false)
	{
		return array(
			'num' => $page_num,
			'url' => $this->get_page_url($page_num) ,
			'is_current' => $is_current,
		);
	}

	/**
	 * @return array
	 */
	protected function create_page_ellipsis()
	{
		return array(
			'num' => '...',
			'url' => null,
			'is_current' => false,
		);
	}

	/**
	 * Render an HTML pagination control.
	 * Bootstrap 4.5.0
	 * @return string
	 */
	public function create_links()
	{
		if ($this->_config['total_pages'] <= 1)
		{
			return '';
		}

		$html = '<ul class="pagination justify-content-center justify-content-sm-start flex-wrap my-3">';
		if ($this->get_prev_url())
		{
			$html .= '<li class="page-item"><a class="page-link text-dark mb-1" href="' . htmlspecialchars($this->get_prev_url()) . '" data-page="' . $this->get_prev_page() . '">' . $this->_config['prev_link'] . '</a></li>';
		}

		foreach ($this->get_pages() as $page)
		{
			if ($page['url'])
			{
				if ($page['is_current'])
				{
					$html .= '<li class="page-item active"><span class="page-link bg-secondary border-secondary mb-1">' . htmlspecialchars($page['num']) . '</span></li>';
				}
				else
				{
					$html .= '<li class="page-item"><a class="page-link text-dark mb-1" href="' . htmlspecialchars($page['url']) . '" data-page="' . htmlspecialchars($page['num']) . '">' . htmlspecialchars($page['num']) . '</a></li>';
				}
			}
			else
			{
				$html .= '<li class="page-item disabled"><span class="page-link mb-1">' . htmlspecialchars($page['num']) . '</span></li>';
			}
		}

		if ($this->get_next_url())
		{
			$html .= '<li class="page-item"><a class="page-link text-dark mb-1" href="' . htmlspecialchars($this->get_next_url()) . '" data-page="' . $this->get_next_page() . '">' . $this->_config['next_link'] . '</a></li>';
		}
		$html .= '</ul>';

		return $html;
	}

	/**
	 * w3css
	 */
	public function w3_create_links()
	{
		if ($this->_config['total_pages'] <= 1)
		{
			return '';
		}

		$html = '<div class="w3-bar w3-center w3-section">';
		if ($this->get_prev_url())
		{
			$html .= '<a class="w3-button w3-padding-small text-decoration-none w3-light-gray m-2 w3-text-blue" href="' . htmlspecialchars($this->get_prev_url()) . '" data-page="' . $this->get_prev_page() . '">' . $this->_config['prev_link'] . '</a>';
		}

		foreach ($this->get_pages() as $page)
		{
			if ($page['url'])
			{
				if ($page['is_current'])
				{
					$html .= '<span class="w3-button w3-blue w3-padding-small text-decoration-none m-2">' . htmlspecialchars($page['num']) . '</span>';
				}
				else
				{
					$html .= '<a class="w3-button w3-padding-small text-decoration-none w3-light-gray m-2 w3-text-blue" href="' . htmlspecialchars($page['url']) . '" data-page="' . htmlspecialchars($page['num']) . '">' . htmlspecialchars($page['num']) . '</a>';
				}
			}
			else
			{
				$html .= '<span class="w3-button w3-light-gray w3-padding-small text-decoration-none m-2">' . htmlspecialchars($page['num']) . '</span>';
			}
		}

		if ($this->get_next_url())
		{
			$html .= '<a class="w3-button w3-padding-small text-decoration-none w3-light-gray m-2 w3-text-blue" href="' . htmlspecialchars($this->get_next_url()) . '" data-page="' . $this->get_next_page() . '">' . $this->_config['next_link'] . '</a>';
		}
		$html .= '</div>';

		return $html;
	}

}

