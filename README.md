Federal Drupal Custom Module Code Samples

## Overview

This repository contains code samples demonstrating DSFederal's expertise in developing custom Drupal modules for enterprise-level applications.

- **Drupal Version**: Drupal 8.8+ / 9.x / 10.x
- **Development Standards**: Drupal Coding Standards

## Custom Modules

### 1. HIT Digital Gov Search (`hit_digital_gov_search`)
**Integration & Search Enhancement**

A custom search module that integrates with the Digital.gov Search service (search.usa.gov) to provide enhanced search capabilities.

**Features:**
- Custom search block with autocomplete functionality
- Integration with USA.gov search API
- Configurable search affiliate settings
- AJAX-powered autocomplete suggestions

**Use Case:** Provides federally-compliant search functionality with autocomplete for improved user experience.

---

### 2. HIT Salesforce Integration (`hit_salesforce_integration`)
**CRM Integration & Data Management**

A comprehensive Webform handler that submits speaker request form data directly to Salesforce CRM via API.

**Features:**
- Custom Webform handler for Salesforce Web-to-Lead integration
- Complex data mapping and transformation
- Field validation and sanitization
- Error handling and logging
- Configurable submission URL

**Use Case:** Streamlines speaker request workflow by automatically creating leads in Salesforce, eliminating manual data entry and ensuring data consistency.

---

### 3. ISA Comment Notification (`isa_comment_notification`)
**Workflow & Notification System**

Automated email notification system for content comments using Rules module integration.

**Features:**
- Rules-based email notifications
- Workbench Access integration for section-based user assignment
- Taxonomy hierarchy traversal for permission management
- Customizable email templates
- Direct database queries for performance optimization

**Use Case:** Notifies assigned users when comments are submitted to the Interoperability platform content they manage.

---

### 4. ISA Search Customization (`isa_search_customization`)
**Search API Enhancement**

Custom Search API processors that enhance search indexing and filtering for specialized content types.

**Features:**
- **Custom Search Processors:**
  - `IsaContentArea`: Categorizes content by area (ISA, USCDI, SVAP)
  - `UscdiClassificationLevel`: Tracks USCDI data classification levels
  - `UscdiDataType`: Distinguishes between Data Classes and Data Elements
  - `UscdiDataElementName`: Extracts and indexes data element names
  - `UscdiDataElementContent`: Scrapes and indexes dynamic page content
  - `AddExtraContent`: Web scraping for supplemental content indexing
- Custom search form for USCDI-specific searches
- Advanced content categorization and faceting

**Use Case:** Enables sophisticated search and filtering across complex healthcare interoperability standards documentation.

---

### 5. ISA Subscription (`isa_subscription`)
**Content Subscription & Notification**

A subscription management system that allows users to subscribe to content changes and receive email notifications.

**Features:**
- Page-level subscriptions for individual content
- Site-wide subscription option for all ISA changes
- Custom database table for subscription tracking
- Automated email notifications on content updates
- Subscription/unsubscription blocks and forms
- Revision-aware subscription triggers
- Confirmation emails for subscribe/unsubscribe actions

**Use Case:** Keeps stakeholders informed of changes to critical healthcare interoperability standards documentation.

---

## Key Technical Capabilities Demonstrated

### API Integration
- External service integration (Digital.gov Search, Salesforce)
- RESTful API consumption
- HTTP client implementations with error handling

### Search & Discovery
- Custom Search API processors
- Advanced indexing strategies
- Web scraping for content augmentation
- Faceted search implementation

### Workflow & Automation
- Rules module integration
- Custom event handling
- Automated notification systems
- Database-driven subscription management

### Content Management
- Workbench Access integration
- Taxonomy-based permissions
- Complex content relationships
- Revision tracking

### Data Processing
- Form data validation and transformation
- Field mapping and sanitization
- Direct database queries for performance
- HTML parsing and content extraction

## Development Practices

### Code Standards
- Adherence to Drupal Coding Standards
- Comprehensive inline documentation
- Object-oriented design patterns
- Dependency injection where applicable

### Security Considerations
- Input validation and sanitization
- SQL injection prevention with parameterized queries
- XSS protection with proper escaping
- Access control integration
- Secure API communications

### Database Design
- Custom schema implementations
- Efficient query optimization
- Proper indexing strategies
- Transaction handling

### Performance Optimization
- Caching strategies
- Efficient database queries
- Lazy loading techniques
- Content scraping with cURL optimization

## Installation & Usage

**Note**: This is a sample repository for demonstration purposes. For production implementations:

1. Review module requirements and dependencies
2. Follow standard Drupal module installation procedures
3. Configure module settings according to project specifications
4. Test thoroughly in development environment before deployment
5. Review and update API endpoints and credentials as needed

## Module Dependencies

- **Core**: Drupal Core 8.8+
- **Contributed Modules**: 
  - Webform (for Salesforce integration)
  - Rules (for comment notifications)
  - Search API (for search customizations)
  - Workbench Access (for permission management)

## Company Information

**DSFederal, Inc.**
- Specialization: Digital services and custom development
- Expertise: Drupal development, system integration, and agile delivery

*This repository demonstrates code samples only and does not contain complete production systems or sensitive client information.*
