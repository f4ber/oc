<?php
class ControllerFeedGoogleSitemap extends Controller {
	public function index() {
		if ($this->config->get('google_sitemap_status')) {
			$output  = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
			$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\r\n";
			$output .= '  <url>' . "\r\n";
			$output .= '    <loc>' . $this->config->get('config_url') . '</loc>' . "\r\n";
			$output .= '    <lastmod>' . date("Y-m-d") . '</lastmod>' . "\r\n"; 
			$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
			$output .= '    <priority>1.0</priority>' . "\r\n";
			$output .= '  </url>' . "\r\n";
			
			$this->load->model('catalog/product');
			$this->load->model('tool/image');

			$products = $this->model_catalog_product->getProducts();

			foreach ($products as $product) {
				if ($product['image']) {
					$output .= '  <url>' . "\r\n";
					$output .= '    <loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>' . "\r\n";
					$output .= '    <lastmod>' . date("Y-m-d") . '</lastmod>' . "\r\n"; 
					$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
					$output .= '    <priority>1.0</priority>' . "\r\n";
					$output .= '  </url>' . "\r\n";
				}
			}

			$this->load->model('catalog/category');

			$output .= $this->getCategories(0);

			$this->load->model('catalog/manufacturer');

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

			foreach ($manufacturers as $manufacturer) {
				$output .= '  <url> . "\r\n"';
				$output .= '    <loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>' . "\r\n";
				$output .= '    <lastmod>' . date("Y-m-d") . '</lastmod>' . "\r\n"; 
				$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
				$output .= '    <priority>0.7</priority>' . "\r\n";
				$output .= '  </url>' . "\r\n";

#				$products = $this->model_catalog_product->getProducts(array('filter_manufacturer_id' => $manufacturer['manufacturer_id']));
#
#				foreach ($products as $product) {
#					$output .= '  <url>' . "\r\n";
#					$output .= '    <loc>' . $this->url->link('product/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . '&amp;product_id=' . $product['product_id']) . '</loc>' . "\r\n";
#					$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
#					$output .= '    <priority>1.0</priority>' . "\r\n";
#					$output .= '  </url>' . "\r\n";
#				}
			}

			$this->load->model('catalog/information');

			$informations = $this->model_catalog_information->getInformations();

			foreach ($informations as $information) {
				$output .= '  <url>' . "\r\n";
				$output .= '    <loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>' . "\r\n";
				$output .= '    <lastmod>' . date("Y-m-d") . '</lastmod>' . "\r\n"; 
				$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
				$output .= '    <priority>0.5</priority>' . "\r\n";
				$output .= '  </url>' . "\r\n";
			}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	protected function getCategories($parent_id, $current_path = '') {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

			$output .= '  <url>' . "\r\n";
			$output .= '    <loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>' . "\r\n";
			$output .= '    <lastmod>' . date("Y-m-d") . '</lastmod>' . "\r\n"; 
			$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
			$output .= '    <priority>0.7</priority>' . "\r\n";
			$output .= '  </url>' . "\r\n";

#			$products = $this->model_catalog_product->getProducts(array('filter_category_id' => $result['category_id']));
#
#			foreach ($products as $product) {
#				$output .= '  <url>' . "\r\n";
#				$output .= '    <loc>' . $this->url->link('product/product', 'path=' . $new_path . '&amp;product_id=' . $product['product_id']) . '</loc>' . "\r\n";
#				$output .= '    <changefreq>weekly</changefreq>' . "\r\n";
#				$output .= '    <priority>1.0</priority>' . "\r\n";
#				$output .= '  </url>' . "\r\n";
#			}

			$output .= $this->getCategories($result['category_id'], $new_path);
		}

		return $output;
	}
}
