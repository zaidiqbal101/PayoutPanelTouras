import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import sidebarData from '../data/sidebar.json';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faMoneyBill,
  faCreditCard,
  faUserPlus,
  faExchangeAlt,
  faList,
  faChevronDown,
  faChevronUp,
} from '@fortawesome/free-solid-svg-icons';

const iconMap = {
  'fa-money-bill': faMoneyBill,
  'fa-credit-card': faCreditCard,
  'fa-user-plus': faUserPlus,
  'fa-exchange-alt': faExchangeAlt,
  'fa-list': faList,
};

const Sidebar = () => {
  const { url } = usePage();
  const [openDropdowns, setOpenDropdowns] = useState({});

  const toggleDropdown = (index) => {
    setOpenDropdowns((prev) => ({
      ...prev,
      [index]: !prev[index],
    }));
  };

  return (
    <div className="w-64 h-screen bg-gray-800 text-white flex flex-col fixed overflow-y-auto">
      <div className="p-4 text-xl font-bold border-b border-gray-700">
        Dashboard
      </div>
      <nav className="flex-1 p-4">
        <ul>
          {sidebarData.sidebar.map((item, index) => (
            <li key={index} className="mb-2">
              {!item.dropdown ? (
                <Link
                  href={item.route}
                  className={`flex items-center p-2 rounded-lg hover:bg-gray-700 transition-colors ${
                    url.startsWith(item.route) ? 'bg-gray-700' : ''
                  }`}
                >
                  <FontAwesomeIcon icon={iconMap[item.icon]} className="mr-3" />
                  <span>{item.name}</span>
                </Link>
              ) : (
                <div>
                  <button
                    onClick={() => toggleDropdown(index)}
                    className="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 transition-colors"
                  >
                    <div className="flex items-center">
                      <FontAwesomeIcon
                        icon={iconMap[item.icon]}
                        className="mr-3"
                      />
                      <span>{item.name}</span>
                    </div>
                    <FontAwesomeIcon
                      icon={openDropdowns[index] ? faChevronUp : faChevronDown}
                    />
                  </button>
                  {openDropdowns[index] && (
                    <ul className="ml-6 mt-1 space-y-1">
                      {item.subMenu.map((subItem, subIndex) => (
                        <li key={subIndex}>
                          <Link
                            href={subItem.route}
                            className={`flex items-center p-2 text-sm rounded-lg hover:bg-gray-700 transition-colors ${
                              url.startsWith(subItem.route) ? 'bg-gray-700' : ''
                            }`}
                          >
                            <FontAwesomeIcon
                              icon={iconMap[subItem.icon]}
                              className="mr-2"
                            />
                            {subItem.name}
                          </Link>
                        </li>
                      ))}
                    </ul>
                  )}
                </div>
              )}
            </li>
          ))}
        </ul>
      </nav>
    </div>
  );
};

export default Sidebar;
