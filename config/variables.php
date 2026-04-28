<?php

$appUrl = rtrim((string) env('APP_URL', 'http://127.0.0.1:8000'), '/');

return [
  "creatorName" => "Toko Online",
  "creatorUrl" => $appUrl,
  "templateName" => "Tokoonline",
  "templateSuffix" => "Laravel Admin Panel",
  "templateVersion" => "2.0.0",
  "templateFree" => true,
  "templateDescription" => "Aplikasi toko online berbasis Laravel dan MySQL dengan dashboard admin bertema Sneat.",
  "templateKeyword" => "toko online, laravel, ecommerce, mysql, dashboard admin",
  "licenseUrl" => "#",
  "livePreview" => $appUrl,
  "productPage" => $appUrl,
  "support" => "#",
  "adminTemplates" => $appUrl . "/admin/dashboard",
  "bootstrapDashboard" => $appUrl . "/admin/products",
  "ogTitle" => "Tokoonline Laravel Admin",
  "ogImage" => "",
  "ogType" => "website",
  "documentation" => "#",
  "repository" => "#",
  "gitRepo" => "#",
  "gitRepoAccess" => "#",
  "githubFreeUrl" => "#",
  "facebookUrl" => "#",
  "twitterUrl" => "#",
  "githubUrl" => "#",
  "dribbbleUrl" => "#",
  "instagramUrl" => "#"
];
