<?php

namespace Concrete\Controller\SinglePage\Dashboard\Multilingual;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Concrete\Core\Multilingual\Page\Section;
use Config;
use Localization;
use Loader;
use Page;

defined('C5_EXECUTE') or die("Access Denied.");

class Setup extends DashboardPageController
{

    public $helpers = array('form');
    protected $pagesToCopy = array();

    public function view()
    {
        $ll = Core::make('localization/languages');
        $languages = $ll->getLanguageList();
        $this->set('pages', Section::getList());
        $this->set('languages', $languages);
        $this->set('ch', Core::make('multilingual/interface/flag'));

        $this->set('defaultLanguage', Config::get('concrete.multilingual.default_language'));
        $this->set('redirectHomeToDefaultLanguage', Config::get('concrete.multilingual.redirect_home_to_default_language'));
        $this->set('useBrowserDetectedLanguage', Config::get('concrete.multilingual.use_browser_detected_language'));
    }

    protected function populateCopyArray($startingPage)
    {
        $db = Loader::db();
        if ($startingPage->isAlias()) {
            $cID = $startingPage->getCollectionPointerOriginalID();
        } else {
            $cID = $startingPage->getCollectionID();
        }

        $q = "select cID from Pages where cParentID = ? order by cDisplayOrder asc";
        $r = $db->query($q, array($cID));
        while ($row = $r->fetchRow()) {
            $c = Page::getByID($row['cID'], 'RECENT');
            if (!$c->getAttribute('multilingual_exclude_from_copy')) {
                $this->pagesToCopy[] = $c;
                $this->populateCopyArray($c);
            }
        }
    }

    public function load_icons()
    {

        $ll = Core::make('localization/languages');
        $ch = Core::make('multilingual/interface/flag');
        $msLanguage = $this->post('msLanguage');

        if (!$msLanguage) {
            return false;
        }

        $countries = $ll->getLanguageCountries($msLanguage);

        asort($countries);
        $i = 1;
        foreach ($countries as $region => $value) {
            $flag = $ch->getFlagIcon($region);
            if ($flag) {
                $checked = "";
                if ($this->post('selectedLanguageIcon') == $region) {
                    $checked = "checked=\"checked\"";
                } else {
                    if ($i == 1 && (!$this->post('selectedLanguageIcon'))) {
                        $checked = "checked=\"checked\"";
                    }
                }
                $html .= '<div class="radio"><label><input type="radio" name="msIcon" ' . $checked . ' id="languageIcon' . $i . '" value="' . $region . '" onchange="ccm_multilingualUpdateLocale(\'' . $region . '\')" /> ' . $flag . ' ' . $value . '</label></div>';
                $i++;
            }
        }

        if ($i == 1) {
            $html = "<div><strong>" . t('None') . "</strong></div>";
        }


        print $html;
        exit;
    }

    public function multilingual_content_enabled()
    {
        $this->set('message', t('Multilingual content enabled'));
        $this->view();
    }

    public function multilingual_content_updated()
    {
        $this->set('message', t('Multilingual content updated'));
        $this->view();
    }

    public function tree_copied()
    {
        $this->set('message', t('Multilingual tree copied.'));
        $this->view();
    }

    public function language_section_removed()
    {
        $this->set('message', t('Language section removed.'));
        $this->view();
    }

    public function default_language_updated()
    {
        $this->set('message', t('Default language settings updated.'));
        $this->view();
    }

    public function set_default()
    {
        if (Loader::helper('validation/token')->validate('set_default')) {
            $lc = Section::getByLocale($this->post('defaultLanguage'));
            if (is_object($lc)) {
                Config::save('concrete.multilingual.default_language', $this->post('defaultLanguage'));
                Config::save('concrete.multilingual.redirect_home_to_default_language', $this->post('redirectHomeToDefaultLanguage'));
                Config::save('concrete.multilingual.use_browser_detected_language', $this->post('useBrowserDetectedLanguage'));
                $this->redirect('/dashboard/multilingual/setup', 'default_language_updated');
            } else {
                $this->error->add(t('Invalid language section'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function remove_language_section($sectionID = false, $token = false)
    {
        if (Loader::helper('validation/token')->validate('', $token)) {
            $lc = Section::getByID($sectionID);
            if (is_object($lc)) {

                $lc->unassign();
                $this->redirect('/dashboard/multilingual/setup', 'language_section_removed');

            } else {
                $this->error->add(t('Invalid language section'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function add_content_section()
    {
        if (Loader::helper('validation/token')->validate('add_content_section')) {
            if ((!Loader::helper('validation/numbers')->integer($this->post('pageID'))) || $this->post('pageID') < 1) {
                $this->error->add(t('You must specify a page for this multilingual content section.'));
            } else {
                $pc = Page::getByID($this->post('pageID'));
            }

            if (!$this->error->has()) {
                $lc = Section::getByID($this->post('pageID'));
                if (is_object($lc)) {
                    $this->error->add(t('A language section page at this location already exists.'));
                }
            }

            if (!$this->error->has()) {
                if ($this->post('msIcon')) {
                    $combination = $this->post('msLanguage') . '_' . $this->post('msIcon');
                    $locale = Section::getByLocale($combination);
                    if (is_object($locale)) {
                        $this->error->add(t('This language/region combination already exists.'));
                    }
                }
            }

            if (!$this->error->has()) {
                Section::assign($pc, $this->post('msLanguage'), $this->post('msIcon'));
                $this->redirect('/dashboard/multilingual/setup', 'multilingual_content_updated');
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

}
