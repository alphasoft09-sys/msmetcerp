<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TcLms;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $sitemap .= '<sitemap>';
        $sitemap .= '<loc>' . url('/sitemap-lms.xml') . '</loc>';
        $sitemap .= '<lastmod>' . now()->toISOString() . '</lastmod>';
        $sitemap .= '</sitemap>';
        $sitemap .= '</sitemapindex>';

        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function lms()
    {
        $lmsSites = TcLms::where('status', 'approved')
            ->where('is_approved', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $sitemap .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        // Add main LMS page
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . url('/lms') . '</loc>';
        $sitemap .= '<lastmod>' . now()->toISOString() . '</lastmod>';
        $sitemap .= '<changefreq>daily</changefreq>';
        $sitemap .= '<priority>1.0</priority>';
        $sitemap .= '</url>';

        // Add each approved LMS site
        foreach ($lmsSites as $site) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . url('/lms/' . $site->seo_slug) . '</loc>';
            $sitemap .= '<lastmod>' . $site->updated_at->toISOString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            
            // Add image sitemap if content has images
            if (strpos($site->site_contents, '<img') !== false) {
                preg_match_all('/<img[^>]+src="([^"]+)"/i', $site->site_contents, $matches);
                foreach ($matches[1] as $imageUrl) {
                    if (strpos($imageUrl, 'http') === 0) {
                        $sitemap .= '<image:image>';
                        $sitemap .= '<image:loc>' . htmlspecialchars($imageUrl) . '</image:loc>';
                        $sitemap .= '<image:title>' . htmlspecialchars($site->site_title) . '</image:title>';
                        $sitemap .= '</image:image>';
                    }
                }
            }
            
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
