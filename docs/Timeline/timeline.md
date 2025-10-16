## Description

A Bootstrap timeline component for displaying chronological events with optional icons, descriptions, and color-coded styling. Fully aligned with the Enabel Bootstrap Theme and using Symfony UX Icons for all iconography.

## Parameters

| Parameter    | Type     | Description                                                | Default |
|:-------------|:---------|:-----------------------------------------------------------|:--------|
| `title`      | `string` | Optional title displayed above the timeline                | `null`  |
| `items`      | `array`  | Array of timeline items to display                         | `[]`    |
| `showColors` | `bool`   | Whether to apply color styling to timeline items           | `false` |

### Item Parameters

Each item in the `items` array supports the following parameters:

| Parameter     | Type     | Description                                                | Default |
|:--------------|:---------|:-----------------------------------------------------------|:--------|
| `date`        | `string` | Date or time label for the timeline item                   | `null`  |
| `label`       | `string` | Main title or label for the timeline item                  | `null`  |
| `description` | `string` | Detailed description (supports HTML content)               | `null`  |
| `icon`        | `string` | Symfony UX Icons identifier (e.g., 'lucide:flag')         | `null`  |
| `color`       | `string` | Bootstrap color variant for the item                       | `null`  |

### Supported Colors

The `color` parameter accepts any of the following Bootstrap theme colors:
- `primary`
- `secondary` 
- `success`
- `danger`
- `warning`
- `info`
- `light`
- `dark`

## Usage

### Basic timeline

```twig
{{ component('Enabel:Ux:Timeline', {
    title: 'Project milestones',
    items: [
        { date: '2024-01-10', label: 'Project start' },
        { date: '2024-03-20', label: 'First milestone' },
        { date: '2024-06-15', label: 'Project completion' }
    ]
}) }}
```

### Timeline with icons and descriptions

```twig
{{ component('Enabel:Ux:Timeline', {
    title: 'Development process',
    items: [
        { 
            date: '2024-01-10', 
            label: 'Kick-off', 
            description: 'Project started successfully',
            icon: 'lucide:flag'
        },
        { 
            date: '2024-03-20', 
            label: 'First delivery', 
            description: 'Initial milestone completed',
            icon: 'lucide:package'
        },
        { 
            date: '2024-06-15', 
            label: 'Final release', 
            description: 'Project completed and delivered',
            icon: 'lucide:check-circle'
        }
    ]
}) }}
```

### Timeline with colors

```twig
{{ component('Enabel:Ux:Timeline', {
    title: 'Development phases',
    showColors: true,
    items: [
        { 
            date: '2024-01-01', 
            label: 'Planning', 
            description: 'Initial planning and requirements gathering',
            icon: 'lucide:clipboard-list',
            color: 'info'
        },
        { 
            date: '2024-02-15', 
            label: 'Development', 
            description: 'Core development phase begins',
            icon: 'lucide:code',
            color: 'primary'
        },
        { 
            date: '2024-04-01', 
            label: 'Testing', 
            description: 'Quality assurance and testing phase',
            icon: 'lucide:bug',
            color: 'warning'
        },
        { 
            date: '2024-05-01', 
            label: 'Deployment', 
            description: 'Production deployment and go-live',
            icon: 'lucide:rocket',
            color: 'success'
        }
    ]
}) }}
```

### Timeline with HTML content

```twig
{{ component('Enabel:Ux:Timeline', {
    title: 'Company milestones',
    showColors: true,
    items: [
        { 
            date: '2020-01-01', 
            label: 'Company Founded', 
            description: 'Our journey begins with a <strong>vision</strong> to transform the industry',
            icon: 'lucide:building',
            color: 'primary'
        },
        { 
            date: '2021-06-15', 
            label: 'First Major Client', 
            description: 'Successfully onboarded our first enterprise client with <em>100+ users</em>',
            icon: 'lucide:users',
            color: 'success'
        }
    ]
}) }}
```

### Minimal timeline

```twig
{{ component('Enabel:Ux:Timeline', {
    items: [
        { date: '2024-01-10', label: 'Start of the year' },
        { date: '2024-06-21', label: 'Start of the summer' },
        { date: '2024-12-31', label: 'End of the year' }
    ]
}) }}
```

### Timeline without dates

```twig
{{ component('Enabel:Ux:Timeline', {
    title: 'Process steps',
    items: [
        { 
            label: 'Step 1: Planning',
            description: 'Define requirements and scope',
            icon: 'lucide:clipboard-list'
        },
        { 
            label: 'Step 2: Development',
            description: 'Build the solution',
            icon: 'lucide:code'
        },
        { 
            label: 'Step 3: Testing',
            description: 'Verify and validate',
            icon: 'lucide:check-circle'
        }
    ]
}) }}
```

## Custom CSS classes

You can add custom CSS classes to the timeline container:

```twig
{{ component('Enabel:Ux:Timeline', {
    title: 'Custom styled timeline',
    items: [
        { date: '2024-01-01', label: 'Event 1' },
        { date: '2024-02-01', label: 'Event 2' }
    ],
    class: 'my-custom-timeline shadow-lg'
}) }}
```

## Responsive behavior

The timeline is fully responsive:
- On larger screens (≥1140px): Timeline items are displayed inline with dates positioned above the timeline
- On smaller screens: Timeline items stack vertically with optimized spacing

## Accessibility

The timeline component includes:
- Semantic HTML structure
- Proper heading hierarchy
- Alt text support for icons through Symfony UX Icons
- Screen reader friendly content organization

## Integration with Enabel Bootstrap Theme

The Timeline component is designed to work seamlessly with the Enabel Bootstrap Theme:
- Uses the exact CSS classes and structure from the theme
- Supports all Bootstrap color variants
- Includes proper responsive breakpoints
- Compatible with both light and dark color schemes

## Icon recommendations

Common Symfony UX Icons for timeline events:

| Icon                    | Use Case                    |
|:------------------------|:----------------------------|
| `lucide:flag`           | Project start, milestones   |
| `lucide:package`        | Deliverables, releases      |
| `lucide:check-circle`   | Completed tasks, success    |
| `lucide:code`           | Development phases          |
| `lucide:bug`            | Testing, debugging          |
| `lucide:rocket`         | Launches, deployments       |
| `lucide:users`          | Team events, meetings       |
| `lucide:building`       | Company events              |
| `lucide:calendar`       | Scheduled events            |
| `lucide:star`           | Achievements, awards        |
| `lucide:globe`          | Global events, expansion    |
| `lucide:trending-up`    | Growth, progress            |
| `lucide:clock`          | Time-sensitive events       |
| `lucide:alert-triangle` | Important notices           |