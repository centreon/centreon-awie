import groovy.json.JsonSlurper

/*
** Variables.
*/
properties([buildDiscarder(logRotator(numToKeepStr: '50'))])
def serie = '20.04'
def maintenanceBranch = "${serie}.x"
env.PROJECT='centreon-awie'

if (env.BRANCH_NAME.startsWith('release-')) {
  env.BUILD = 'RELEASE'
} else if ((env.BRANCH_NAME == 'master') || (env.BRANCH_NAME == maintenanceBranch)) {
  env.BUILD = 'REFERENCE'
} else {
  env.BUILD = 'CI'
}

/*
** Pipeline code.
*/
stage('Source') {
  node {
    sh 'setup_centreon_build.sh'
    dir('centreon-awie') {
      checkout scm
    }
    sh "./centreon-build/jobs/awie/${serie}/mon-awie-source.sh"
    source = readProperties file: 'source.properties'
    env.VERSION = "${source.VERSION}"
    env.RELEASE = "${source.RELEASE}"
    publishHTML([
      allowMissing: false,
      keepAll: true,
      reportDir: 'summary',
      reportFiles: 'index.html',
      reportName: 'Centreon AWIE Build Artifacts',
      reportTitles: ''
    ])
  }
}

try {
  stage('Unit tests') {
    parallel 'centos7': {
      node {
        sh 'setup_centreon_build.sh'
        /*
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-unittest.sh centos7"
        junit 'ut.xml'
        if (currentBuild.result == 'UNSTABLE')
          currentBuild.result = 'FAILURE'
        step([
          $class: 'CloverPublisher',
          cloverReportDir: '.',
          cloverReportFileName: 'coverage.xml'
        ])
        step([
          $class: 'hudson.plugins.checkstyle.CheckStylePublisher',
          pattern: 'codestyle.xml',
          usePreviousBuildAsReference: true,
          useDeltaValues: true,
          failedNewAll: '0'
        ])
        */

        // Run sonarQube analysis
        withSonarQubeEnv('SonarQubeDev') {
          sh "./centreon-build/jobs/awie/${serie}/mon-awie-analysis.sh"
        }
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Unit tests stage failure.');
    }
  }

  // sonarQube step to get qualityGate result
  stage('Quality gate') {
      node {
        def reportFilePath = "target/sonar/report-task.txt"
        def reportTaskFileExists = fileExists "${reportFilePath}"
        if (reportTaskFileExists) {
          echo "Found report task file"
          def taskProps = readProperties file: "${reportFilePath}"
          echo "taskId[${taskProps['ceTaskId']}]"
          timeout(time: 10, unit: 'MINUTES') {
            while (true) {
              sleep 10
              def taskStatusResult    =
              sh(returnStdout: true, script: "curl -s -X GET -u ${authString} \'${sonarProps['sonar.host.url']}/api/ce/task?id=${taskProps['ceTaskId']}\'")
              echo "taskStatusResult[${taskStatusResult}]"
              def taskStatus  = new JsonSlurper().parseText(taskStatusResult).task.status
              echo "taskStatus[${taskStatus}]"
              // Status can be SUCCESS, ERROR, PENDING, or IN_PROGRESS. The last two indicate it's
              // not done yet.
              if (taskStatus != "IN_PROGRESS" && taskStatus != "PENDING") {
                break;
              }
              def qualityGate = waitForQualityGate()
              if (qualityGate.status != 'OK') {
                currentBuild.result = 'FAIL'
              }
            }
          }
        }
        if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
          error("Quality gate failure: ${qualityGate.status}.");
        }
      }
    }

  stage('Package') {
    parallel 'centos7': {
      node {
        sh 'setup_centreon_build.sh'
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-package.sh centos7"
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Package stage failure.');
    }
  }

  stage('Bundle') {
    parallel 'centos7': {
      node {
        sh 'setup_centreon_build.sh'
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-bundle.sh centos7"
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Bundle stage failure.');
    }
  }

  stage('Acceptance tests') {
    parallel 'centos7': {
      node {
        sh 'setup_centreon_build.sh'
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-acceptance.sh centos7"
        junit 'xunit-reports/**/*.xml'
        if (currentBuild.result == 'UNSTABLE')
          currentBuild.result = 'FAILURE'
        archiveArtifacts allowEmptyArchive: true, artifacts: 'acceptance-logs/*.txt, acceptance-logs/*.png'
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Acceptance tests stage failure.');
    }
  }

  if ((env.BUILD == 'RELEASE') || (env.BUILD == 'REFERENCE')) {
    stage('Delivery') {
      node {
        sh 'setup_centreon_build.sh'
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-delivery.sh"
      }
      if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
        error('Delivery stage failure.');
      }
    }
  }
} catch(e) {
  if ((env.BUILD == 'RELEASE') || (env.BUILD == 'REFERENCE')) {
    slackSend channel: "#monitoring-metrology", color: "#F30031", message: "*FAILURE*: `CENTREON AWIE` <${env.BUILD_URL}|build #${env.BUILD_NUMBER}> on branch ${env.BRANCH_NAME}\n*COMMIT*: <https://github.com/centreon/centreon-awie/commit/${source.COMMIT}|here> by ${source.COMMITTER}\n*INFO*: ${e}"
  }
  currentBuild.result = 'FAILURE'
}
